<?php

namespace App\Mappers;

use DocuSign\eSign\Model\Document;
use DocuSign\eSign\Model\EnvelopeDefinition;
use DocuSign\eSign\Model\FullName;
use DocuSign\eSign\Model\Signer;
use DocuSign\eSign\Model\SignHere;
use DocuSign\eSign\Model\Tabs;

/**
 * Class DocumentsMapper
 *
 * @package App\Mappers
 */
class DocumentsMapper
{
    /**
     * Map documents
     *
     * @param array $documents
     * @return EnvelopeDefinition
     */
    public function map(array $documents): EnvelopeDefinition
    {
        $documentInstances = [];
        $signers   = [];

        foreach($documents as $document) {
            $documentInstances[] = $this->prepareDocumentRow($document);

            foreach($document['recipients'] as $recipient) {
                $signers[] = $this->createSigner($recipient);
            }
        }

        return new EnvelopeDefinition([
            'email_subject' => "Please sign this document sent from the PHP SDK",
            'documents'     => $documentInstances,
            'recipients'    => [
                'signers' => $signers,
            ],
            'status'        => "created",
        ]);
    }

    /**
     * Prepare document row
     *
     * @param array $row
     * @return Document
     */
    protected function prepareDocumentRow(array $row): Document
    {
        $content        = file_get_contents($row['documentPath']);
        $contentEncoded = base64_encode($content);

        return new Document([
            'document_base64' => $contentEncoded,
            'name'            => 'Example document',
            'file_extension'  => 'pdf',
            'document_id'     => $row['id'],
        ]);
    }

    /**
     * Create signer
     *
     * @param array $row
     * @return Signer
     */
    protected function createSigner(array $row): Signer
    {
        $signer = new Signer(
            [
                'email'           => $row['email'],
                'name'            => $row['name'],
                'recipient_id'    => $row['id'],
                'routing_order'   => 1,
                'recipient_type'  => 'signer',
                'delivery_method' => 'email',
                'status'          => 'created',
                'role_name'       => 'signer',
            ]
        );

        $signHereTab = new SignHere(
            [
                'tab_label'       => 'signHere',
                'anchor_string'   => 'Employee Signature:',
                'anchor_units'    => 'pixels',
                'anchor_y_offset' => '7',
                'anchor_x_offset' => '140'
            ]
        );

        $employeeNameTab = new FullName([
            'anchor_string'   => 'Employee Name:',
            'anchor_units'    => 'pixels',
            'anchor_y_offset' => '-7',
            'anchor_x_offset' => '120',
            'font_size'       => 'Size20',
        ]);

        $signer->settabs(new Tabs(['sign_here_tabs' => [$signHereTab], 'full_name_tabs' => [$employeeNameTab]]));

        return $signer;
    }
}