<?php

namespace App\Mappers;

use DocuSign\eSign\Model\BulkSendingCopy;
use DocuSign\eSign\Model\BulkSendingCopyRecipient;
use DocuSign\eSign\Model\BulkSendingList;

/**
 * Class BulkSendingListMapper
 *
 * @package App\Mappers
 */
class BulkSendingListMapper
{
    /**
     * Map recipients
     *
     * @param array $recipients
     * @return BulkSendingList
     */
    public function map(array $recipients): BulkSendingList
    {
        $result = [];

        foreach($recipients as $recipient) {
            $result[] = $this->getBulkSendingCopy($recipient);
        }

        $bulkSendingList = $this->getBulkSendingList();
        $bulkSendingList->setBulkCopies($result);

        return $bulkSendingList;
    }

    /**
     * Get bulk sending copy
     *
     * @param array $row
     * @return BulkSendingCopy
     */
    protected function getBulkSendingCopy(array $row): BulkSendingCopy
    {
        return new BulkSendingCopy([
            'recipients'    => [$this->createRecipient($row)],
            'custom_fields' => [],
        ]);
    }

    public function getBulkSendingList(): BulkSendingList
    {
        return new BulkSendingList(['name' => 'sample']);
    }

    /**
     * Create recipient
     *
     * @param array $recipient
     * @return BulkSendingCopyRecipient
     */
    protected function createRecipient(array $recipient): BulkSendingCopyRecipient
    {
        return new BulkSendingCopyRecipient([
            'role_name' => 'signer',
            'tabs'      => [],
            'name'      => $recipient['name'],
            'email'     => $recipient['email'],
        ]);
    }
}