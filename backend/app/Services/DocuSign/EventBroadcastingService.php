<?php

namespace App\Services\DocuSign;

use App\Enums\EventsTypesEnum;
use App\Events\EnvelopeEvent;
use App\Models\Employee;
use DocuSign\Admin\Client\Auth\OAuthToken;
use Exception;
use Illuminate\Support\Arr;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class EventBroadcastingService
 *
 * @package App\Services\DocuSign
 */
class EventBroadcastingService
{
    /**
     * Send event
     *
     * @param array $parameters
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function send(array $parameters): bool
    {
        $event      = Arr::get($parameters, 'event');
        $recipients = $this->getRecipients($parameters);

        if (!count($recipients) || !$event) {
            return false;
        }

        /** @var OAuthToken $docuSignToken */
        $docuSignToken   = app(AuthService::class)->requestToken(config('settings.docusign.user_id'));
        $envelopeService = new EnvelopeService(null, $docuSignToken->getAccessToken());
        $tokenId         = $envelopeService->getTokenIdForEnvelope(Arr::get($parameters, 'data.envelopeId'));

        if (!$tokenId) {
            return false;
        }

        foreach($recipients as $recipient) {
            $employee = app(Employee::class)->getByExtId($recipient['userId']);

            event(new EnvelopeEvent(
                $this->getEventKey($event),
                $tokenId,
                $recipient['name'],
                $this->getCreatedAt($event, $parameters)
            ));
        }

        return true;
    }

    /**
     * Get recipients
     *
     * @param array $parameters
     * @return array
     */
    protected function getRecipients(array $parameters): array
    {
        return Arr::get($parameters, 'data.envelopeSummary.recipients.signers', []);
    }

    /**
     * Get created date/time
     *
     * @param string $event
     * @param array $parameters
     * @return string|null
     */
    protected function getCreatedAt(string $event, array $parameters): ?string
    {
        return match($event) {
            'recipient-signed',
            'recipient-completed',
            'envelope-signed',
            'envelope-completed'     => Arr::get($parameters, 'data.envelopeSummary.completedDateTime'),
            'envelope-deleted',
            'envelope-voided'        => Arr::get($parameters, 'data.envelopeSummary.voidedDateTime'),
            'recipient-declined',
            'envelope-declined'      => Arr::get($parameters, 'data.envelopeSummary.declinedDateTime'),
            'envelope-created'       => Arr::get($parameters, 'data.envelopeSummary.createdDateTime'),
            'envelope-corrected',
            'recipient-reassign',
            'recipient-resent',
            'envelope-resent',
            'recipient-sent',
            'envelope-sent'          => Arr::get($parameters, 'data.envelopeSummary.sentDateTime'),
            'envelope-delivered',
            'recipient-delivered'    => Arr::get($parameters, 'data.envelopeSummary.deliveredDateTime'),
            'recipient-finish-later' => Arr::get($parameters, 'data.envelopeSummary.statusChangedDateTime'),
            default                  => null
        };
    }

    /**
     * Get event key
     *
     * @param string $event
     * @return string|null
     */
    protected function getEventKey(string $event): ?string
    {
        return match($event) {
            'envelope-completed',
            'envelope-signed'        => EventsTypesEnum::ENVELOPE_SIGNED,
            'envelope-deleted'       => EventsTypesEnum::ENVELOPE_DELETED,
            'envelope-voided'        => EventsTypesEnum::ENVELOPE_VOIDED,
            'envelope-declined'      => EventsTypesEnum::ENVELOPE_DECLINED,
            'envelope-sent'          => EventsTypesEnum::ENVELOPE_SENT,
            'envelope-created'       => EventsTypesEnum::ENVELOPE_CREATED,
            'envelope-resent'        => EventsTypesEnum::ENVELOPE_RESENT,
            'envelope-corrected'     => EventsTypesEnum::ENVELOPE_CORRECTED,
            'envelope-delivered'     => EventsTypesEnum::ENVELOPE_DELIVERED,
            'recipient-sent'         => EventsTypesEnum::RECIPIENT_SENT,
            'recipient-delivered'    => EventsTypesEnum::RECIPIENT_DELIVERED,
            'recipient-signed',
            'recipient-completed'    => EventsTypesEnum::RECIPIENT_SIGNED,
            'recipient-resent'       => EventsTypesEnum::RECIPIENT_RESENT,
            'recipient-declined'     => EventsTypesEnum::RECIPIENT_DECLINED,
            'recipient-reassign'     => EventsTypesEnum::RECIPIENT_REASSIGNED,
            'recipient-finish-later' => EventsTypesEnum::RECIPIENT_FINISH_LATER,
            default                  => null
        };
    }
}