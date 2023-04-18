<?php

namespace App\Services\DocuSign;

use DocuSign\eSign\Model\BulkSendRequest;

/**
 * Class BulkRequestFactory
 *
 * @package App\Services\DocuSign
 */
class BulkRequestFactory
{
    /**
     * Get bulk request instance
     *
     * @param string $envelopeId
     * @return BulkSendRequest
     */
    public static function factory(string $envelopeId): BulkSendRequest
    {
        return app(
            BulkSendRequest::class,
            [
                'data' => [
                    'envelope_or_template_id' => $envelopeId,
                ],
            ]
        );
    }
}