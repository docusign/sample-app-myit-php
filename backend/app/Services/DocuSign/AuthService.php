<?php

namespace App\Services\DocuSign;

use App\Services\DocuSign\Cache\TokenCacheService;
use DocuSign\Admin\Client\ApiClient;
use DocuSign\Admin\Client\Auth\OAuth;
use DocuSign\Admin\Client\Auth\OAuthToken;
use Exception;

/**
 * Class AuthService
 *
 * @package App\Services\DocuSign
 */
class AuthService
{
    /**
     * Scope for token
     */
    protected const SCOPE = [
        'signature',
        'organization_read',
        'permission_read',
        'user_write',
        'group_read',
        'user_read',
        'account_read',
        'domain_read',
        'identity_provider_read',
    ];

    /**
     * @var string|null
     */
    protected string|null $errorMessage = null;

    /**
     * @param TokenCacheService $tokenCacheService
     */
    public function __construct(protected TokenCacheService $tokenCacheService)
    {
    }

    /**
     * Login
     *
     * @param string $tokenId
     * @return bool
     */
    public function login(string $tokenId): bool
    {
        try {
            $response = $this->requestToken(config('settings.docusign.user_id'));

            $this->tokenCacheService->save($tokenId, $response->getAccessToken(), $response->getExpiresIn());
        } catch (Exception $e) {
            $this->errorMessage = $e->getMessage();

            return false;
        }

        return true;
    }

    /**
     * Request token from a server
     *
     * @param string $userId
     * @return OAuthToken|null
     * @throws Exception
     */
    public function requestToken(string $userId): ?OAuthToken
    {
        $apiClient = new ApiClient(null, $this->getOAuth());

        try {
            return $apiClient->requestJWTUserToken(
                config('settings.docusign.client_id'),
                $userId,
                $this->getPrivateKey(),
                self::SCOPE
            )[0];
        } catch (Exception $e) {
            if (str_contains($e->getMessage(), 'consent_required')) {
                $authorizationURL = config('settings.docusign.account_url') . '/oauth/auth?' . http_build_query(
                        [
                            'scope'         => "impersonation+" . implode(' ', self::SCOPE),
                            'redirect_uri'  => route('callback-url'),
                            'client_id'     => config('settings.docusign.client_id'),
                            'response_type' => 'code',
                        ]
                    );

                throw new Exception("Paste URL '$authorizationURL' into browser");
            } else {
                throw new Exception($e->getMessage());
            }
        }
    }

    /**
     * Get JWT
     *
     * @param string $tokenId
     * @return string|null
     */
    public function getJWT(string $tokenId): ?string
    {
        return $this->tokenCacheService->get($tokenId);
    }

    /**
     * Get error message
     *
     * @return string|null
     */
    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    /**
     * Get oAuth instance
     *
     * @return OAuth
     */
    protected function getOAuth(): OAuth
    {
        $oAuth = app(OAuth::class);
        $oAuth->setOAuthBasePath(config('settings.docusign.account_url'));

        return $oAuth;
    }

    /**
     * Get private key
     *
     * @return string
     */
    protected function getPrivateKey(): string
    {
        return file_get_contents(storage_path(config('settings.docusign.private_key_path')), true);
    }
}