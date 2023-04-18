<?php

namespace App\Services\DocuSign;

use DocuSign\Admin\Client\ApiClient;
use DocuSign\Admin\Configuration;

/**
 * Class BaseAdminService
 *
 * @package App\Services\DocuSign
 */
abstract class BaseAdminService extends BaseService
{
    /**
     * Create configuration instance
     *
     * @return Configuration
     */
    protected function createConfiguration(): Configuration
    {
        return new Configuration();
    }

    /**
     * Get API client
     *
     * @param $configurationInstance
     * @return ApiClient
     */
    protected function getApiClient($configurationInstance): ApiClient
    {
        return app(ApiClient::class, ['config' => $configurationInstance]);
    }

    /**
     * Get base URL
     *
     * @return string
     */
    protected function getBaseUrl():string
    {
        return config('settings.docusign.api_url');
    }
}