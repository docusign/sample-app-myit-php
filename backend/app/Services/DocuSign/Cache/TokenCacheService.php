<?php

namespace App\Services\DocuSign\Cache;

use Illuminate\Support\Facades\Cache;

/**
 * Class TokenCacheService
 *
 * @package App\Services\DocuSign\Cache
 */
class TokenCacheService
{
    /**
     * Cache key prefix
     */
    protected const KEY_PREFIX = 'docusign_token_';

    /**
     * Get token from cache
     *
     * @param string $tokenId
     * @return string|null
     */
    public function get(string $tokenId): ?string
    {
        return Cache::get($this->buildKey($tokenId));
    }

    /**
     * Save token to cache
     *
     * @param string $tokenId
     * @param string $token
     * @param int $expiresInSeconds
     * @return void
     */
    public function save(string $tokenId, string $token, int $expiresInSeconds)
    {
        Cache::add($this->buildKey($tokenId), $token, $expiresInSeconds);
    }

    /**
     * Clear token
     *
     * @param string $tokenId
     * @return void
     */
    public function clear(string $tokenId)
    {
        Cache::forget($this->buildKey($tokenId));
    }

    /**
     * Build cache key
     *
     * @param string $tokenId
     * @return string
     */
    protected function buildKey(string $tokenId): string
    {
        return self::KEY_PREFIX . $tokenId;
    }
}