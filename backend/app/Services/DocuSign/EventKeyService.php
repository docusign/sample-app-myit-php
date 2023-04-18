<?php

namespace App\Services\DocuSign;

/**
 * Class EventKeyService
 *
 * @package App\Services\DocuSign
 */
class EventKeyService
{
    /**
     * Create key
     *
     * @param array $event
     * @return string
     */
    public function build(array $event): string
    {
        return "{$event['object']}_{$event['action']}";
    }
}