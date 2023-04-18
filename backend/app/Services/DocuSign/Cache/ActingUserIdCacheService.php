<?php

namespace App\Services\DocuSign\Cache;

use Illuminate\Support\Facades\Cache;

/**
 * Class ActingUserIdCacheService
 *
 * @package App\Services\DocuSign\Cache
 */
class ActingUserIdCacheService
{
    /**
     * Cache key prefix
     */
    protected const KEY_PREFIX = 'docusign_acting_user_';

    /**
     * Get token ID from cache
     *
     * @param string $userExtId
     * @return string
     */
    public function getTokenId(string $userExtId): string
    {
        return Cache::get($this->buildKey($userExtId, 'token'));
    }

    /**
     * Get user ID from cache
     *
     * @param string $tokenId
     * @return string
     */
    public function getUserExtId(string $tokenId): string
    {
        return Cache::get($this->buildKey($tokenId, 'user_id'));
    }

    /**
     * Add token ID and user ext ID to cache
     *
     * @param string $tokenId
     * @param string $userExtId
     * @return void
     */
    public function add(string $tokenId, string $userExtId)
    {
        Cache::forever($this->buildKey($userExtId, 'token'), $tokenId);
        Cache::forever($this->buildKey($tokenId, "user_id"), $userExtId);
    }

    /**
     * Clear user ID
     *
     * @param string $tokenId
     * @return void
     */
    public function clear(string $tokenId)
    {
        $userId = $this->getUserExtId($tokenId);

        Cache::forget($this->buildKey($userId, 'token'));
        Cache::forget($this->buildKey($tokenId, 'user_id'));
    }

    /**
     * Build cache key
     *
     * @param string $key
     * @return string
     */
    protected function buildKey(string $key, string $type): string
    {
        return self::KEY_PREFIX . $type . '_' . $key;
    }
}