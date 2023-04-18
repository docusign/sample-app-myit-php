<?php

namespace App\Services\DocuSign;

use App\Models\Envelope;
use DocuSign\eSign\Api\BulkEnvelopesApi;
use DocuSign\eSign\Client\ApiException;
use Illuminate\Support\Collection;

/**
 * Class EnvelopeService
 *
 * @package App\Services\DocuSign
 */
class EnvelopeService extends BaseEsignService
{
    /**
     * Get token ID for envelope
     *
     * @param string $envelopeId
     * @return string|null
     * @throws ApiException
     */
    public function getTokenIdForEnvelope(string $envelopeId): ?string
    {
        $envelope = app(Envelope::class)->getByExtId($envelopeId);

        if ($envelope) {
            return $envelope->token_id;
        }

        $envelope = $this->getFromEnvelopeId($envelopeId);

        return $envelope->token_id ?? null;
    }

    /**
     * Prepare envelopes
     *
     * @param Collection $envelopes
     * @return Collection
     * @throws ApiException
     */
    public function prepareEnvelopes(Collection $envelopes): Collection
    {
        $envelopeApi = new BulkEnvelopesApi($this->apiClient);

        foreach($envelopes as $envelope) {
            if (!$envelope->ext_id) {
                $extId = $this->getExtId($envelopeApi, $envelope->batch_id);

                if (!$extId) {
                    continue;
                }

                $envelope->ext_id = $extId;
                $envelope->save();
            }
        }

        return $envelopes;
    }

    /**
     * Get from envelope ID
     *
     * @param string $envelopeId
     * @return Envelope|null
     * @throws ApiException
     */
    protected function getFromEnvelopeId(string $envelopeId): ?Envelope
    {
        $envelopes   = app(Envelope::class)->getWithoutExtId();
        $envelopeApi = new BulkEnvelopesApi($this->apiClient);

        foreach($envelopes as $envelope) {
            $extId = $this->getExtId($envelopeApi, $envelope->batch_id);

            if (!$extId) {
                continue;
            }

            $envelope->update(['ext_id' => $extId]);

            if ($envelopeId === $extId) {
                return $envelope;
            }
        }

        return null;
    }

    /**
     * Get ext ID
     *
     * @param BulkEnvelopesApi $envelopeApi
     * @param string $batchId
     * @return string|null
     * @throws ApiException
     */
    protected function getExtId(BulkEnvelopesApi $envelopeApi, string $batchId): ?string
    {
        $response  = $envelopeApi->getBulkSendBatchEnvelopes(config('settings.docusign.account_id'), $batchId);
        $envelopes = $response->getEnvelopes();

        if (!$envelopes || !count($envelopes)) {
            return null;
        }

        return $envelopes[0]->getEnvelopeId();
    }
}