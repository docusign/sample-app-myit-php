<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\Employee;
use App\Services\CurrentUser;
use App\Services\DocuSign\Cache\ActingUserIdCacheService;
use App\Services\DocuSign\Cache\TokenCacheService;
use App\Services\DocuSign\DeletePermissionProfilesService;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * Class LogoutController
 *
 * @package App\Http\Controllers\Api
 */
class LogoutController extends BaseController
{
    /**
     * Logout action
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function __invoke(): JsonResponse
    {
        $tokenId = app(CurrentUser::class)->getTokenId();
        app(TokenCacheService::class)->clear($tokenId);
        app(CurrentUser::class)->get()->token()->revoke();
        $permissionProfileService = app(DeletePermissionProfilesService::class, compact('tokenId'));
        $permissionProfileService->delete(Employee::all());
        app(ActingUserIdCacheService::class)->clear($tokenId);

        return response()->json([
            'success' => true,
        ]);
    }
}