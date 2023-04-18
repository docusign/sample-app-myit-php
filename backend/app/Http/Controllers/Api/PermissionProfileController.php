<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Api\DeletePermissionProfileRequest;
use App\Http\Requests\Api\SendPermissionProfileRequest;
use App\Http\Resources\PermissionProfilesResource;
use App\Models\Employee;
use App\Models\PermissionProfile;
use App\Services\CurrentUser;
use App\Services\DocuSign\AssignPermissionProfilesService;
use App\Services\DocuSign\DeletePermissionProfilesService;
use DocuSign\Admin\Client\ApiException;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Class PermissionProfileController
 *
 * @package App\Http\Controllers\Api
 */
class PermissionProfileController extends BaseController
{
    /**
     * Get a list of permission profiles
     *
     * @param PermissionProfile $permissionProfile
     * @return AnonymousResourceCollection
     */
    public function index(PermissionProfile $permissionProfile): AnonymousResourceCollection
    {
        $profiles = $permissionProfile->all();

        return PermissionProfilesResource::collection($profiles);
    }

    /**
     * Create permission profile
     *
     * @param SendPermissionProfileRequest $request
     * @return JsonResponse
     * @throws ApiException
     * @throws Exception
     */
    public function create(SendPermissionProfileRequest $request): JsonResponse
    {
        $permissionProfileService = app(
            AssignPermissionProfilesService::class,
            [
                'tokenId' => app(CurrentUser::class)->getTokenId(),
            ]
        );
        $permissionProfileService->assign($request->get('employees'));

        return response()->json(['success' => true]);
    }

    /**
     * Delete permission profiles
     *
     * @param DeletePermissionProfileRequest $request
     * @return JsonResponse
     * @throws ApiException
     * @throws Exception
     */
    public function delete(DeletePermissionProfileRequest $request, Employee $employee): JsonResponse
    {
        $employees = $employee->query()->find($request->get('employees_ids'));
        $tokenId   = app(CurrentUser::class)->getTokenId();
        $permissionProfileService = app(DeletePermissionProfilesService::class, compact('tokenId'));
        $permissionProfileService->delete($employees);

        return response()->json(['success' => true]);
    }
}