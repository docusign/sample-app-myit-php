<?php

namespace App\Services\DocuSign;

use DocuSign\eSign\Client\ApiClient;
use DocuSign\eSign\Configuration;

/**
 * Class BaseEsignService
 *
 * @package App\Services\DocuSign
 */
abstract class BaseEsignService extends BaseService
{
    /**
     * Get configuration instance
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
        return new ApiClient($configurationInstance);
    }
}