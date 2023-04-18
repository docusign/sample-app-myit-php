<?php

namespace App\Services\DocuSign;

use App\Models\Employee;
use App\Models\PermissionProfile;
use DocuSign\Admin\Api\UsersApi;
use DocuSign\Admin\Client\ApiException;
use DocuSign\Admin\Model\PermissionProfileRequest;
use DocuSign\Admin\Model\UpdateMembershipRequest;
use DocuSign\Admin\Model\UpdateUserRequest;
use DocuSign\Admin\Model\UpdateUsersRequest;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * Class AssignPermissionProfilesService
 *
 * @package App\Services\DocuSign
 */
class AssignPermissionProfilesService extends BaseAdminService
{
    /**
     * Create permission profiles
     *
     * @param array $employees
     * @return void
     * @throws ApiException
     * @throws Exception
     */
    public function assign(array $employees)
    {
        $employeesCollection = $this->getEmployeesFromDB(Arr::pluck($employees, 'id'));
        $profilesCollection  = $this->getPermissionProfilesFromStorage(Arr::pluck($employees, 'permission_profile_id'));

        $userRequests = [];
        foreach($employees as $employee) {
            $user    = $employeesCollection->where('id', $employee['id'])->first();
            $profile = $profilesCollection->where('id', $employee['permission_profile_id'])->first();

            $user->permission_profile_id = $profile->id;

            $userRequests[] = $this->createUserRequest($user, $profile);
        }

        $updateUserRequests  = $this->createUserRequests($userRequests);
        $organizationService = app(OrganizationService::class, ['tokenId' => $this->tokenId]);
        $userApi             = app(UsersApi::class, ['apiClient' => $this->apiClient]);

        $userApi->updateUser($organizationService->getDefaultOrganizationId(), $updateUserRequests);

        $employeesCollection->each(function (Employee $employee) {
            $employee->save();
        });
    }

    /**
     * Get employees from database
     *
     * @param array $ids
     * @return Collection
     */
    protected function getEmployeesFromDB(array $ids): Collection
    {
        return app(Employee::class)->query()->find($ids);
    }

    /**
     * Get permission profiles from storage
     *
     * @param array $ids
     * @return Collection
     */
    protected function getPermissionProfilesFromStorage(array $ids): Collection
    {
        return app(PermissionProfile::class)->query()->find($ids);
    }

    /**
     * Create user requests
     *
     * @param array $requests
     * @return UpdateUsersRequest
     */
    protected function createUserRequests(array $requests): UpdateUsersRequest
    {
        return new UpdateUsersRequest(['users' => $requests]);
    }

    /**
     * Create user request
     *
     * @param Employee $employee
     * @param PermissionProfile $permissionProfile
     * @return UpdateUserRequest
     */
    protected function createUserRequest(
        Employee $employee,
        PermissionProfile $permissionProfile
    ): UpdateUserRequest {
        $profileRequest    = $this->createPermissionProfileRequest($permissionProfile);
        $membershipRequest = $this->createMembershipRequest($profileRequest);

        return new UpdateUserRequest([
            'id'          => $employee->ext_id,
            'memberships' => [$membershipRequest],
            'site_id'     => $employee->site_id,
        ]);
    }

    /**
     * Create permission profile request
     *
     * @param PermissionProfile $permissionProfile
     * @return PermissionProfileRequest
     */
    protected function createPermissionProfileRequest(PermissionProfile $permissionProfile): PermissionProfileRequest
    {
        return new PermissionProfileRequest([
            'id' => $permissionProfile->ext_id,
        ]);
    }

    /**
     * Create membership request
     *
     * @param PermissionProfileRequest $profileRequest
     * @return UpdateMembershipRequest
     */
    protected function createMembershipRequest(PermissionProfileRequest $profileRequest): UpdateMembershipRequest
    {
        return new UpdateMembershipRequest([
            'account_id' => config('settings.docusign.account_id'),
            'permission_profile' => $profileRequest,
        ]);
    }
}