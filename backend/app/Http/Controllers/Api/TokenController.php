<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Services\CurrentUser;
use Illuminate\Http\JsonResponse;

/**
 * Class TokenController
 *
 * @package App\Http\Controllers\Api
 */
class TokenController extends BaseController
{
    /**
     * Get token action
     *
     * @param CurrentUser $currentUser
     * @return JsonResponse
     */
    public function __invoke(CurrentUser $currentUser): JsonResponse
    {
        return response()->json([
            'tokenId' => $currentUser->getTokenId(),
        ]);
    }
}