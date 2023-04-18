<?php

namespace App\Services\DocuSign;

use DocuSign\Monitor\Client\ApiClient;
use DocuSign\Monitor\Configuration;

/**
 * Class BaseMonitorService
 *
 * @package App\Services\DocuSign
 */
abstract class BaseMonitorService extends BaseService
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
        return config('settings.docusign.monitor_url');
    }
}