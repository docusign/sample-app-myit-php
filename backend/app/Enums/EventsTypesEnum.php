<?php

namespace App\Enums;

/**
 * Class EventsTypesEnum
 *
 * @package App\Enums
 */
class EventsTypesEnum
{
    /**
     * Envelope sent event
     */
    const ENVELOPE_SENT = 'Envelope_Sent';

    /**
     * Envelope signed event
     */
    const ENVELOPE_SIGNED = 'Envelope_Signed';

    /**
     * Envelope voided event
     */
    const ENVELOPE_VOIDED = 'Envelope_Voided';

    /**
     * Envelope deleted event
     */
    const ENVELOPE_DELETED = 'Envelope_Deleted';

    /**
     * Envelope declined event
     */
    const ENVELOPE_DECLINED = 'Envelope_Declined';

    /**
     * Envelope created event
     */
    const ENVELOPE_CREATED = 'Envelope_Created';

    /**
     * Envelope delivered event
     */
    const ENVELOPE_DELIVERED = 'Envelope_Delivered';

    /**
     * Envelope resent event
     */
    const ENVELOPE_RESENT = 'Envelope_Resent';

    /**
     * Envelope corrected event
     */
    const ENVELOPE_CORRECTED = 'Envelope_Corrected';

    /**
     * Recipient sent event
     */
    const RECIPIENT_SENT = 'Recipient_Sent';

    /**
     * Recipient delivered event
     */
    const RECIPIENT_DELIVERED = 'Recipient_Delivered';

    /**
     * Recipient signed event
     */
    const RECIPIENT_SIGNED = 'Recipient_Signed';

    /**
     * Recipient resent event
     */
    const RECIPIENT_RESENT = 'Recipient_Resent';

    /**
     * Recipient declined event
     */
    const RECIPIENT_DECLINED = 'Recipient_Declined';

    /**
     * Recipient reassigned event
     */
    const RECIPIENT_REASSIGNED = 'Recipient_Reassigned';

    /**
     * Recipient finish later event
     */
    const RECIPIENT_FINISH_LATER = 'Recipient_FinishLater';

    /**
     * User updated event
     */
    const USER_UPDATED = 'User_Updated';

    /**
     * Get all
     *
     * @return string[]
     */
    public function getAll(): array
    {
        return [
            self::ENVELOPE_SENT,
            self::ENVELOPE_SIGNED,
            self::ENVELOPE_VOIDED,
            self::ENVELOPE_DELETED,
            self::ENVELOPE_DECLINED,
            self::ENVELOPE_CREATED,
            self::ENVELOPE_DELIVERED,
            self::ENVELOPE_RESENT,
            self::ENVELOPE_CORRECTED,
            self::USER_UPDATED,
            self::RECIPIENT_SENT,
            self::RECIPIENT_DELIVERED,
            self::RECIPIENT_SIGNED,
            self::RECIPIENT_RESENT,
            self::RECIPIENT_REASSIGNED,
            self::RECIPIENT_DECLINED,
            self::RECIPIENT_FINISH_LATER,
        ];
    }
}