<?php

namespace App\Services\DocuSign;

use DocuSign\Admin\Api\AccountsApi;
use DocuSign\Admin\Client\ApiException;
use DocuSign\Admin\Model\PermissionProfileResponse;

/**
 * Class PermissionProfileService
 *
 * @package App\Services\DocuSign
 */
class PermissionProfileService extends BaseAdminService
{
    /**
     * Get permission profiles from server
     *
     * @return PermissionProfileResponse[]
     * @throws ApiException
     */
    public function getListFromServer(): array
    {
        $organizationService = app(OrganizationService::class, ['tokenId' => $this->tokenId]);
        $accountsApi         = app(AccountsApi::class, ['apiClient' => $this->apiClient]);

        $permissions = $accountsApi->getPermissions(
            $organizationService->getDefaultOrganizationId(),
            config('settings.docusign.account_id')
        );

        return $permissions->getPermissions();
    }
}