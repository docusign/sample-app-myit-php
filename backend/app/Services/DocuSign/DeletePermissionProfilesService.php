<?php

namespace App\Services\DocuSign;

use App\Models\PermissionProfile;
use App\Services\EmployeeService;
use DocuSign\Admin\Client\ApiException;
use Exception;
use Illuminate\Support\Collection;

/**
 * Class DeletePermissionProfilesService
 *
 * @package App\Services\DocuSign
 */
class DeletePermissionProfilesService extends BaseAdminService
{
    /**
     * Delete permission profiles
     *
     * @param Collection $employees
     * @return void
     * @throws ApiException
     * @throws Exception
     */
    public function delete(Collection $employees)
    {
        $employeeService    = new EmployeeService($this->tokenId);
        $permissionProfiles = PermissionProfile::all();

        $users = [];

        foreach($employees as $employee) {
            $defaultPermissionProfileName = $employeeService->getDefaultPermissionProfileNameByEmployeeName($employee->name);
            $users[] = [
                'id'                    => $employee->id,
                'permission_profile_id' => $permissionProfiles->where('name', $defaultPermissionProfileName)->first()->id,
            ];
        }

        $assignProfileService = new AssignPermissionProfilesService($this->tokenId);
        $assignProfileService->assign($users);
    }
}