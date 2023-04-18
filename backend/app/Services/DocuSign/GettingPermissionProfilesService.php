<?php

namespace App\Services\DocuSign;

use App\Models\Employee;
use App\Models\PermissionProfile;
use App\Services\EmployeeService;
use DocuSign\Admin\Client\ApiException;
use DocuSign\Admin\Model\PermissionProfileResponse;
use Exception;
use Illuminate\Support\Collection;

/**
 * Class GettingPermissionProfilesService
 *
 * @package App\Services\DocuSign
 */
class GettingPermissionProfilesService extends BaseAdminService
{
    /**
     * Get permission profiles
     *
     * @return Collection
     * @throws ApiException
     * @throws Exception
     */
    public function get(): Collection
    {
        $permissionProfileService = new PermissionProfileService($this->tokenId);
        $profiles = $permissionProfileService->getListFromServer();
        $permissionProfiles = $this->getFromDatabase($profiles);
        $this->addExtId($permissionProfiles, $profiles);

        return $permissionProfiles;
    }

    /**
     * Get with users
     *
     * @return Collection
     * @throws ApiException
     * @throws Exception
     */
    public function getWithUsers(): Collection
    {
        $permissionProfiles = $this->get();
        $this->addEmployees($permissionProfiles);

        return $permissionProfiles;
    }

    /**
     * Get from database
     *
     * @param array $profiles
     * @return Collection
     */
    protected function getFromDatabase(array $profiles): Collection
    {
        $names = array_map(fn(PermissionProfileResponse $item) => $item->getName(), $profiles);

        return PermissionProfile::query()
            ->with(['equipments', 'software'])
            ->whereIn('name', $names)
            ->get();
    }

    /**
     * Add external ID
     *
     * @param Collection $permissionProfiles
     * @param array $profilesFromServer
     * @return void
     */
    protected function addExtId(Collection $permissionProfiles, array $profilesFromServer)
    {
        foreach($profilesFromServer as $profile) {
            $permissionProfile = $permissionProfiles->where('name', $profile->getName())->first();
            $permissionProfile->ext_id = $profile->getId();
        }
    }

    /**
     * Add employees
     *
     * @param Collection $permissionProfiles
     * @return void
     * @throws Exception
     */
    protected function addEmployees(Collection $permissionProfiles)
    {
        $employees = $this->addPermissionProfileExtIdForEmployees();

        foreach($permissionProfiles as $profile) {
            $profile->employees = $employees->filter(function(Employee $employee) use ($profile) {
                return $employee->permission_profile_ext_id === $profile->ext_id;
            });
        }
    }

    /**
     * Get employees
     *
     * @return Collection
     * @throws Exception
     */
    protected function addPermissionProfileExtIdForEmployees(): Collection
    {
        $employees = app(Employee::class)->getByTokenId($this->tokenId);
        $employees = $employees->filter(fn(Employee $employee) => $employee->ext_id !== null);
        $employeeService = new EmployeeService($this->tokenId);

        foreach($employees as $employee) {
            $user = $employeeService->getByEmail($employee->email);
            $membership = $user->getMemberships()[0];
            $permissionProfile = $membership->getPermissionProfile();
            $employee->permission_profile_ext_id = $permissionProfile->getId();
        }

        return $employees;
    }
}