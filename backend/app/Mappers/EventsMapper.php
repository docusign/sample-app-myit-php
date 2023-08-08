<?php

namespace App\Mappers;

use App\Enums\EventsTypesEnum;
use App\Services\CurrentUser;
use App\Services\DocuSign\EventKeyService;
use App\Services\EmployeeService;
use Illuminate\Support\Arr;

/**
 * Class EventsMapper
 *
 * @package App\Mappers
 */
class EventsMapper
{
    /**
     * @var array
     */
    protected array $users = [];

    /**
     * @var EmployeeService
     */
    protected EmployeeService $employeeService;

    /**
     * @param EventKeyService $keyService
     * @param CurrentUser $currentUser
     */
    public function __construct(protected EventKeyService $keyService, CurrentUser $currentUser)
    {
        $this->employeeService = app(EmployeeService::class, ['tokenId' => $currentUser->getTokenId()]);
    }

    /**
     * Map events
     *
     * @param array $events
     * @param array $users
     * @return array
     */
    public function map(array $events, array $users): array
    {
        $this->users = $users;
        $result      = [];

        foreach($events as $event) {
            $result = array_merge($result, $this->mapEvent($event));
        }

        return $result;
    }

    /**
     * Map event
     *
     * @param array $event
     * @return array
     */
    protected function mapEvent(array $event): array
    {
        $users = $this->getUsers($event);

        if (count($users) === 0) {
            return [
                [
                    'event'      => $this->keyService->build($event),
                    'user'       => null,
                    'createdAt'  => $event['timestamp'],
                    'id' => $event['eventId']
                ]
            ];
        }

        return array_map(function (string|null $name) use ($event) {
            return [
                'event'      => $this->keyService->build($event),
                'user'       => $name == null ? "" : $name,
                'createdAt'  => $event['timestamp'],
                'id' => $event['eventId']
            ];
        }, $users);
    }

    /**
     * Get users
     *
     * @param array $event
     * @return array
     */
    protected function getUsers(array $event): array
    {
        $extIds = $this->getUserExtIds($event);

        return array_map(function (string|null $extId) {
            return Arr::get($this->users, $extId);
        }, $extIds);
    }

    /**
     * Get users ext IDs
     *
     * @param array $event
     * @return array
     */
    protected function getUserExtIds(array $event): array
    {
        switch ($this->keyService->build($event)) {
            case EventsTypesEnum::USER_UPDATED:
                return [Arr::get($event, 'data')->AffectedUserId];
                break;
            case EventsTypesEnum::ENVELOPE_SIGNED:
                return [Arr::get($event, 'data')->RecipientInfo->RecipientId];
                break;
            case EventsTypesEnum::ENVELOPE_DELETED:
                return [];
                break;
            case EventsTypesEnum::ENVELOPE_VOIDED:
                return null !== Arr::get($event, 'affectedUserId') ? [Arr::get($event, 'affectedUserId')] : [];
                break;
            case EventsTypesEnum::ENVELOPE_SENT:
                return array_map(fn ($user) => $user->UserId, Arr::get($event, 'data')->RecipientList);
                break;
            default:
                return [];
                break;
        }
    }
}