<?php

namespace App\Services\DocuSign;

use App\Enums\EventsTypesEnum;
use App\Mappers\EventsMapper;
use App\Models\Employee;
use App\Models\Envelope;
use App\Services\CurrentUser;
use App\Services\DocuSign\Cache\EnvelopesRecipientsCacheService;
use App\Services\EmployeeService;
use Carbon\Carbon;
use DocuSign\Monitor\Api\DataSetApi;
use DocuSign\Monitor\Client\ApiException;
use DocuSign\Monitor\Model\AggregateResult;
use DocuSign\Monitor\Model\WebQuery;
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
     * Limit of events
     */
    protected const EVENTS_LIMIT = 1000;

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

        $result = $dataSetApi->postWebQuery('monitor', '2.0', $this->createOptions());

        $events = $this->filterEvents($this->parseResponse($result), $users);

        return $this->mapEvents($events, $users);
    }

    /**
     * Create options
     *
     * @return WebQuery
     */
    protected function createOptions(): WebQuery
    {
        return app(WebQuery::class)
            ->setFilters($this->getFilters())
            ->setAggregations($this->getAggregations());
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
        $events = $this->filterEventsByActions($events);

        return $this->filterByEntities($events, $users);
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

            return match($key) {
                EventsTypesEnum::USER_UPDATED => in_array(Arr::get($event, 'data')->AffectedUserId, array_keys($users)),
                EventsTypesEnum::ENVELOPE_SIGNED,
                EventsTypesEnum::ENVELOPE_SENT,
                EventsTypesEnum::ENVELOPE_VOIDED,
                EventsTypesEnum::ENVELOPE_DECLINED => !!$envelopes->where('ext_id', Arr::get($event, 'data')->EnvelopeId)->first(),
                default => false
            };
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
     * Get filters
     *
     * @return object[]
     */
    protected function getFilters(): array
    {
        $dateFilter      = [
            'FilterName' => 'Time',
            'BeginTime'  => Carbon::parse(app(CurrentUser::class)->getToken()->created_at)->startOfDay()->format('Y-m-d H:i:s'),
            'EndTime'    => now()->endOfDay()->format('Y-m-d H:i:s'),
        ];
        $accountIdFilter = [
            'FilterName' => 'Has',
            'ColumnName' => 'AccountId',
            'Value'      => config('settings.docusign.account_id'),
        ];
        $objectsFilter   = [
            'FilterName' => 'In',
            'ColumnName' => 'Object',
            'Values'     => ['Envelope', 'User', 'Recipient'],
        ];

        return [
            (object) $dateFilter,
            (object) $objectsFilter,
            (object) $accountIdFilter,
        ];
    }

    /**
     * Get aggregations
     *
     * @return object[]
     */
    protected function getAggregations(): array
    {
        $rawAggregation = [
            'aggregationName' => 'Raw',
            'limit'           => self::EVENTS_LIMIT,
            'orderby'         => ['Timestamp, desc'],
        ];

        return [
            (object) $rawAggregation,
        ];
    }

    /**
     * Parse response
     *
     * @param AggregateResult $response
     * @return object[]
     */
    protected function parseResponse(AggregateResult $response): array
    {
        return $response->getResult()[0]->getData();
    }
}