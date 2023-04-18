<?php

namespace App\Services\DocuSign;

use App\Exceptions\AuthenticationException;
use App\Services\DocuSign\Cache\TokenCacheService;
use DocuSign\Admin\Client\ApiClient as AdminApiClient;
use DocuSign\Admin\Configuration as AdminConfiguration;
use DocuSign\eSign\Client\ApiClient as ESignApiClient;
use DocuSign\eSign\Configuration as ESignConfiguration;
use DocuSign\Monitor\Client\ApiClient as MonitorApiClient;
use DocuSign\Monitor\Configuration as MonitorConfiguration;
use Exception;

/**
 * Class BaseService
 *
 * @package App\Services\DocuSign
 */
abstract class BaseService
{
    /**
     * @var ESignApiClient|AdminApiClient|MonitorApiClient
     */
    protected ESignApiClient|AdminApiClient|MonitorApiClient $apiClient;

    /**
     * @param string|null $tokenId
     * @param string|null $docuSignToken
     * @throws Exception
     */
    public function __construct(protected string|null $tokenId, protected string|null $docuSignToken = null)
    {
        $this->initApiClient();
    }

    /**
     * Get account ID from configuration
     *
     * @return string
     */
    protected function getAccountId(): string
    {
        return config('settings.docusign.account_id');
    }

    /**
     * Init API client
     *
     * @param string|null $docuSignToken
     * @return void
     * @throws Exception
     */
    protected function initApiClient()
    {
        $confInstance = $this->createConfiguration();
        $confInstance->setHost($this->getBaseUrl());
        $accessToken  = $this->docuSignToken ?? $this->getAccessToken();

        if (!$accessToken && $this->canRefreshToken()) {
            $accessToken = $this->refreshAccessToken();
        }

        $confInstance->addDefaultHeader('Authorization', "Bearer $accessToken");
        $this->apiClient = $this->getApiClient($confInstance);
    }

    /**
     * Get access token
     *
     * @return string|null
     */
    protected function getAccessToken(): ?string
    {
        return app(TokenCacheService::class)->get($this->tokenId);
    }

    /**
     * Can refresh token
     *
     * @return bool
     */
    protected function canRefreshToken(): bool
    {
        return config('settings.docusign.refresh_token_automatically');
    }

    /**
     * Refresh access token
     *
     * @return string|null
     * @throws Exception
     */
    protected function refreshAccessToken(): ?string
    {
        $authService = app(AuthService::class);

        if (!$authService->login($this->tokenId)) {
            throw new AuthenticationException($authService->getErrorMessage());
        }

        return $this->getAccessToken();
    }

    /**
     * Get base URL
     *
     * @return string
     */
    protected function getBaseUrl():string
    {
        return config('settings.docusign.base_url');
    }

    /**
     * Get configuration class
     *
     * @return ESignConfiguration|AdminConfiguration|MonitorConfiguration
     */
    abstract protected function createConfiguration(): ESignConfiguration|AdminConfiguration|MonitorConfiguration;

    /**
     * Get API client class name
     *
     * @param ESignConfiguration|AdminConfiguration $configurationInstance
     * @return ESignApiClient|AdminApiClient|MonitorApiClient
     */
    abstract protected function getApiClient(
        ESignConfiguration|AdminConfiguration $configurationInstance
    ): ESignApiClient|AdminApiClient|MonitorApiClient;
}