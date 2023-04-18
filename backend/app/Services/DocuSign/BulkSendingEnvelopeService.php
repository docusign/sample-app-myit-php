<?php

namespace App\Services\DocuSign;

use App\Enums\DocumentItemTypesEnum;
use App\Mappers\BulkSendingListMapper;
use App\Mappers\DocumentsMapper;
use App\Models\DocumentItem;
use App\Models\Employee;
use App\Models\Envelope;
use App\Services\DocuSign\Cache\EnvelopesCacheService;
use App\Services\DocuSign\Cache\EnvelopesRecipientsCacheService;
use App\Services\DocuSign\Cache\RecipientsByTokenCacheService;
use Barryvdh\DomPDF\Facade\Pdf;
use DocuSign\eSign\Api\BulkEnvelopesApi;
use DocuSign\eSign\Api\EnvelopesApi;
use DocuSign\eSign\Client\ApiException;
use DocuSign\eSign\Model\BulkSendingList;
use DocuSign\eSign\Model\EnvelopeDefinition;
use DocuSign\eSign\Model\EnvelopeSummary;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * Class BulkSendingEnvelopeService
 *
 * @package App\Services\DocuSign
 */
class BulkSendingEnvelopeService extends BaseEsignService
{
    /**
     * @var BulkEnvelopesApi
     */
    protected BulkEnvelopesApi $bulkEnvelopesApi;

    /**
     * @var EnvelopesApi
     */
    protected EnvelopesApi $envelopesApi;

    /**
     * BulkSendingEnvelopeService constructor
     *
     * @param string|null $tokenId
     * @throws Exception
     */
    public function __construct(
        protected string|null $tokenId
    ) {
        parent::__construct($tokenId);

        $this->initEnvelopeClients();
    }

    /**
     * Send envelope
     *
     * @param array $recipients
     * @return bool
     * @throws ApiException
     */
    public function send(array $recipients): bool
    {
        $recipients = $this->addRecipientsData($recipients);
        $documents  = $this->createDocuments($recipients);
        $documents  = $this->prepareExcludedDocuments($documents);
        $documents  = $this->generateDocuments($documents);

        foreach($documents as $document) {
            $bulkSendingList = $this->createSendingList([$document]);
            $envelope        = $this->createEnvelope([$document]);

            $batchId = $this->sendRequest($envelope['envelope_id'], $bulkSendingList['list_id']);

            $this->addEnvelopeToDB($batchId);

            unlink($document['documentPath']);
        }

        $this->saveRecipientsDocumentItems($recipients);

        return true;
    }

    /**
     * Add envelope to DB
     *
     * @param string $batchId
     * @return void
     */
    protected function addEnvelopeToDB(string $batchId)
    {
        app(Envelope::class)->create([
            'batch_id' => $batchId,
            'token_id' => $this->tokenId,
        ]);
    }

    /**
     * Add recipients data from database
     *
     * @param array $recipients
     * @return array
     */
    protected function addRecipientsData(array $recipients): array
    {
        $employees = $this->getEmployeesFromDB(Arr::pluck($recipients, 'id'));

        return array_map(function (array $recipient) use ($employees) {
            $employee = $employees->where('id', $recipient['id'])->first();

            $recipient['email'] = empty($recipient['email']) ? $employee->email : $recipient['email'];
            $recipient['name']  = empty($recipient['name']) ? $employee->name : $recipient['name'];

            return $recipient;
        }, $recipients);
    }

    /**
     * Save recipients document items
     *
     * @param array $recipients
     * @return void
     */
    protected function saveRecipientsDocumentItems(array $recipients)
    {
        $employees = $this->getEmployeesFromDB(Arr::pluck($recipients, 'id'));
        $employees->load('documentItems');

        foreach($recipients as $recipient) {
            /** @var Employee $employee */
            $employee = $employees->where('id', $recipient['id'])->first();
            $items    = [
                ...$recipient['equipment_ids'],
                ...$recipient['software_ids'],
                ...$employee->documentItems->pluck('id')->toArray()
            ];
            $employee->documentItems()->sync(array_unique($items));
        }
    }

    /**
     * Get employees from database
     *
     * @param array $ids
     * @return Collection
     */
    protected function getEmployeesFromDB(array $ids): Collection
    {
        return app(Employee::class)->find($ids);
    }

    /**
     * Create documents with recipients
     *
     * @param array $recipients
     * @return array
     */
    protected function createDocuments(array $recipients): array
    {
        $groups = [];
        foreach($recipients as $recipient) {
            $items = array_merge($recipient['equipment_ids'], $recipient['software_ids']);
            sort($items);
            $documentId    = implode('', $items);
            $documentItems = DocumentItem::query()->find($items);

            $groups[$documentId]['items']['equipments'] = $this->prepareGroupDocumentItem(
                $documentItems,
                DocumentItemTypesEnum::EQUIPMENT
            );
            $groups[$documentId]['items']['software'] = $this->prepareGroupDocumentItem(
                $documentItems,
                DocumentItemTypesEnum::SOFTWARE
            );
            $groups[$documentId]['id'] = $documentId;

            $groups[$documentId]['recipients'][] = Arr::only($recipient, ['id', 'name', 'email']);
        }

        return array_values($groups);
    }

    /**
     * Add excluded documents parameter for recipients
     *
     * @param array $documents
     * @return array
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function prepareExcludedDocuments(array $documents): array
    {
        $ids = Arr::pluck($documents, 'id');

        foreach($documents as $index => $document) {
            $excludedIds = $ids;
            unset($excludedIds[array_search($document['id'], $excludedIds)]);

            foreach($document['recipients'] as $recipientIndex => $recipient) {
                $documents[$index]['recipients'][$recipientIndex]['excludedDocumentIds'] = array_values($excludedIds);
            }
        }

        return $documents;
    }

    /**
     * Prepare group item
     *
     * @param Collection $documentItems
     * @param string $type
     * @return array
     */
    protected function prepareGroupDocumentItem(Collection $documentItems, string $type): array
    {
        return $documentItems->filter(fn($item) => $item->type === $type)
            ->pluck('name')
            ->toArray();
    }

    /**
     * Generate documents
     *
     * @param array $groups
     * @return array
     */
    protected function generateDocuments(array $groups): array
    {
        foreach($groups as $index => $group) {
            $pdf = Pdf::loadView('document', [
                'equipments' => $group['items']['equipments'],
                'software'   => $group['items']['software'],
            ]);
            $fileName = 'temp/' . md5(uniqid('', true) . now()) . '.pdf';
            $pdf->save($fileName);
            $groups[$index]['documentPath'] = $fileName;
        }

        return $groups;
    }

    /**
     * Create sending list
     *
     * @param array $groups
     * @return BulkSendingList
     * @throws ApiException
     */
    protected function createSendingList(array $groups): BulkSendingList
    {
        $recipients = [];
        foreach($groups as $group) {
            $recipients = [
                ...$recipients,
                ...$group['recipients'],
            ];
        }

        $recipients = $this->prepareRecipientsList($recipients);

        return $this->bulkEnvelopesApi->createBulkSendList($this->getAccountId(), $recipients);
    }

    /**
     * Create envelope
     *
     * @param array $groups
     * @return EnvelopeSummary
     * @throws ApiException
     */
    protected function createEnvelope(array $groups): EnvelopeSummary
    {
        $documents = $this->prepareDocumentsList($groups);

        return $this->envelopesApi->createEnvelope($this->getAccountId(), $documents);
    }

    /**
     * Prepare recipients list
     *
     * @param array $recipients
     * @return BulkSendingList
     */
    protected function prepareRecipientsList(array $recipients): BulkSendingList
    {
        /** @var BulkSendingListMapper $mapper */
        $mapper = app(BulkSendingListMapper::class);

        return $mapper->map($recipients);
    }

    /**
     * Prepare documents list
     *
     * @param array $documents
     * @return EnvelopeDefinition
     */
    protected function prepareDocumentsList(array $documents): EnvelopeDefinition
    {
        $mapper = app(DocumentsMapper::class);

        return $mapper->map($documents);
    }

    /**
     * Send request
     *
     * @param string $envelopeId
     * @param string $bulkListId
     * @return string
     * @throws ApiException
     */
    protected function sendRequest(string $envelopeId, string $bulkListId): string
    {
        $bulkSendRequest = BulkRequestFactory::factory($envelopeId);
        $batch           = $this->bulkEnvelopesApi->createBulkSendRequest(
            $this->getAccountId(),
            $bulkListId,
            $bulkSendRequest
        );
        $this->bulkEnvelopesApi->getBulkSendBatchStatus($this->getAccountId(), $batch['batch_id']);

        $this->bulkEnvelopesApi->getBulkSendBatchEnvelopes(env('DOCUSIGN_ACCOUNT_ID'), $batch['batch_id']);

        return $batch['batch_id'];
    }

    /**
     * Init envelope clients
     *
     * @return void
     */
    protected function initEnvelopeClients()
    {
        $this->bulkEnvelopesApi = app(BulkEnvelopesApi::class, ['apiClient' => $this->apiClient]);
        $this->envelopesApi     = app(EnvelopesApi::class, ['apiClient' => $this->apiClient]);
    }
}