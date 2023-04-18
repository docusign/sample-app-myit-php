<?php

namespace App\Services\DocuSign;

use DocuSign\Admin\Api\AccountsApi;
use DocuSign\Admin\Client\ApiException;

/**
 * Class OrganizationService
 *
 * @package App\Services\DocuSign
 */
class OrganizationService extends BaseAdminService
{
    /**
     * Get organizations
     *
     * @return array
     * @throws ApiException
     */
    public function get(): array
    {
        $accountsApi   = app(AccountsApi::class, ['apiClient' => $this->apiClient]);
        $organizations = $accountsApi->getOrganizations();

        return $organizations->getOrganizations();
    }

    /**
     * Get default organization ID
     *
     * @return string
     * @throws ApiException
     */
    public function getDefaultOrganizationId(): string
    {
        if ($id = config('settings.docusign.organization_id')) {
            return $id;
        }

        $organizations = $this->get();

        return $organizations[0]['id'];
    }
}