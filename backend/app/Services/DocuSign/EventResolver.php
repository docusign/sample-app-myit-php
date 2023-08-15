<?php
namespace App\Services\DocuSign;
use App\Enums\EventsTypesEnum;
use App\Mappers\EventsMapper;
use App\Models\Employee;
use App\Models\Envelope;
use DocuSign\Monitor\Api\DataSetApi;
use DocuSign\Monitor\Api\DataSetApi\GetStreamOptions;
use DocuSign\Monitor\Client\ApiException;
use DocuSign\Monitor\Model\CursoredResult;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\CircularDependencyException;
use Illuminate\Support\Arr;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
/**
 * Class EventResolver
 *
 * @package App\Services\DocuSign
 */
class EventResolver extends BaseMonitorService
{
    /**
     * Get monitor alerts
     *
     * @return array
     * @throws ApiException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function get(): array
    {
        $dataSetApi = new DataSetApi($this->apiClient);
        $users = $this->getUsers();

        $options = $this->createStreamOptions();

        $result = $dataSetApi->getStream('monitor', '2.0', $options);

        $events = $this->filterEvents($this->parseResponse($result), $users);

        return $this->mapEvents($events, $users);
    }

    /**
     * Create stream options to get the data for the last hour
     *
     * @return GetStreamOptions
     */
    protected function createStreamOptions(): GetStreamOptions
    {
        $currentTime = time();
        $oneHourAgo = ($currentTime - 3600); 
        $formattedDate = gmdate('Y-m-d\TH:i:s\Z', $oneHourAgo);

        $options = new GetStreamOptions();
        $options->setCursor($formattedDate);

        return $options;
    }

    /**
     * Get users list
     *
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function getUsers(): array
    {
        return app(Employee::class)
            ->getByTokenId($this->tokenId)
            ->pluck('name', 'ext_id')
            ->toArray();
    }

    /**
     * Filter events
     *
     * @param array $events
     * @param array $users
     * @return array
     * @throws BindingResolutionException
     * @throws CircularDependencyException
     */
    protected function filterEvents(array $events, array $users): array
    {
        $events = $this->filterEventsByAccountId($events);
        $events = $this->filterEventsByActions($events);

        return $this->filterByEntities($events, $users);
    }

    /**
     * Filter events by account id
     *
     * @param array $events
     * @return array
     * @throws BindingResolutionException
     * @throws CircularDependencyException
     */
    protected static function filterEventsByAccountId(array $events): array
    {
        return array_filter($events, function (array $event) {
            return config('settings.docusign.account_id') == $event['accountId'];
        });
    }

    /**
     * Filter events by actions
     *
     * @param array $events
     * @return array
     * @throws BindingResolutionException
     * @throws CircularDependencyException
     */
    protected function filterEventsByActions(array $events): array
    {
        $keyService = app(EventKeyService::class);

        return array_filter($events, function (array $event) use ($keyService) {
            $key = $keyService->build($event);

            return in_array($key, app(EventsTypesEnum::class)->getAll());
        });
    }

    /**
     * Filter by employees
     *
     * @param array $events
     * @param array $users
     * @return array
     * @throws BindingResolutionException
     * @throws CircularDependencyException
     */
    protected function filterByEntities(array $events, array $users): array
    {
        $keyService = app(EventKeyService::class);
        $envelopes  = app(Envelope::class)->getByTokenId($this->tokenId);
        $envelopes = app(EnvelopeService::class, ['tokenId' => $this->tokenId])->prepareEnvelopes($envelopes);

        return array_filter($events, function (array $event) use ($keyService, $users, $envelopes) {
            $key = $keyService->build($event);
            switch($key) {
                case EventsTypesEnum::USER_UPDATED:
                    if (in_array(Arr::get($event, 'data')->AffectedUserId, array_keys($users))) {
                        return true;
                    }
                    break;
                case EventsTypesEnum::ENVELOPE_SIGNED:
                    return true;
                    break;
                case EventsTypesEnum::ENVELOPE_SENT:
                    return true;
                    break;
                case EventsTypesEnum::ENVELOPE_VOIDED:
                    return true;
                    break;
                case EventsTypesEnum::ENVELOPE_DECLINED:
                    if (!!$envelopes->where('ext_id', Arr::get($event, 'data')->EnvelopeId)->first()){
                        return true;
                    }
                    break;
                default:
                    return false;
            };

            return false;
        });
    }
    /**
     * Map events
     *
     * @param array $events
     * @param array $users
     * @return array
     */
    protected function mapEvents(array $events, array $users): array
    {
        return app(EventsMapper::class)->map($events, $users);
    }

    /**
     * Parse response
     *
     * @param CursoredResult $response
     * @return object[]
     */
    protected function parseResponse(CursoredResult $response): array
    {
        return $response->getData();
    }
}
