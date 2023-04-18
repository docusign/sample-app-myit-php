<?php

namespace App\Services;

use App\Enums\PermissionProfileNamesEnum;
use App\Models\Employee;
use App\Models\PermissionProfile;
use App\Services\DocuSign\BaseAdminService;
use App\Services\DocuSign\Cache\ActingUserIdCacheService;
use App\Services\DocuSign\OrganizationService;
use DocuSign\Admin\Api\UsersApi;
use DocuSign\Admin\Client\ApiException;
use DocuSign\Admin\Model\NewAccountUserRequest;
use DocuSign\Admin\Model\NewUserRequestAccountProperties;
use DocuSign\Admin\Model\NewUserResponse;
use DocuSign\Admin\Model\PermissionProfileRequest;
use DocuSign\Admin\Model\UserDrilldownResponse;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * Class EmployeeService
 *
 * @package App\Services
 */
class EmployeeService extends BaseAdminService
{
    /**
     * Generate employees
     *
     * @return void
     * @throws ApiException
     * @throws Exception
     */
    public function generate()
    {
        $permissionProfiles = PermissionProfile::all();

        $users = [];
        foreach(config('settings.employees') as $user) {
            $number            = random_int(1, 9) . random_int(1, 9);
            $hash              = md5(uniqid('', true) . date('Y-m-d H:i:s'));
            $permissionProfile = $this->getPermissionProfile($user['profile'], $permissionProfiles);

            $users[] = [
                'name'                      => $user['name'],
                'email'                     => str_replace('{KEY}', $hash, $user['email']),
                'display_email'             => str_replace('{KEY}', $number, $user['email']),
                'token_id'                  => $this->tokenId,
                'permission_profile_id'     => $permissionProfile->id,
                'permission_profile_ext_id' => $permissionProfile->ext_id,
                'created_at'                => now(),
                'updated_at'                => now(),
            ];
        }

        $users = $this->createEmployeesOnServer($users);

        Employee::query()->insert($users);
    }

    /**
     * Get permission profile
     *
     * @param string $code
     * @param Collection $profiles
     * @return PermissionProfile
     * @throws Exception
     */
    protected function getPermissionProfile(string $code, Collection $profiles): PermissionProfile
    {
        $name = Arr::get(app(PermissionProfileNamesEnum::class)->getAll(), $code);

        if (!$name) {
            throw new Exception("Permission profile $code does not exist");
        }

        return $profiles->where('name', $name)->first();
    }

    /**
     * Create users on server
     *
     * @param array $users
     * @return array
     * @throws ApiException
     */
    protected function createEmployeesOnServer(array $users): array
    {
        foreach($users as $index => $user) {
            $userResponse = $this->createUserOnServer($user);

            $users[$index]['ext_id']  = $userResponse->getId();
            $users[$index]['site_id'] = $userResponse->getSiteId();

            unset($users[$index]['permission_profile_ext_id']);
        }

        return $users;
    }

    /**
     * Create acting user
     *
     * @param string $tokenId
     * @return void
     * @throws ApiException
     */
    public function createActingUser(string $tokenId)
    {
        $email = str_replace('{KEY}', uniqid('', true), config('settings.acting_user'));

        $user = $this->createUserOnServer([
            'permission_profile_ext_id' => PermissionProfile::query()->find(PermissionProfile::ADMIN_ID)->ext_id,
            'name'                      => 'Manager',
            'email'                     => $email,
        ], $this->docuSignToken);

        app(ActingUserIdCacheService::class)->add($tokenId, $user->getId());
    }

    /**
     * Create user on the server
     *
     * @param array $user
     * @return NewUserResponse
     * @throws ApiException
     * @throws Exception
     */
    protected function createUserOnServer(array $user, string $docuSignToken = null): NewUserResponse
    {
        $userApi = new UsersApi($this->apiClient);

        $profile = new PermissionProfileRequest([
            'id' => $user['permission_profile_ext_id'],
        ]);

        $accountInfo = new NewUserRequestAccountProperties([
            'id'                 => config('settings.docusign.account_id'),
            'permission_profile' => $profile,
        ]);

        $request = new NewAccountUserRequest([
            'user_name'          => $user['name'],
            'email'              => $user['email'],
            'default_account_id' => config('settings.docusign.account_id'),
            'accounts'           => [$accountInfo],
        ]);

        $organizationApi = new OrganizationService($this->tokenId, $docuSignToken);

        return $userApi->addUsers(
            $organizationApi->getDefaultOrganizationId(),
            config('settings.docusign.account_id'),
            $request
        );
    }

    /**
     * Get by email
     *
     * @param string $email
     * @return UserDrilldownResponse|null
     * @throws ApiException
     */
    public function getByEmail(string $email): ?UserDrilldownResponse
    {
        $organizationApi = new OrganizationService($this->tokenId);

        $userApi = new UsersApi($this->apiClient);
        $options = new UsersApi\GetUserProfilesOptions();
        $options->setEmail($email);

        $response = $userApi->getUserProfiles(
            $organizationApi->getDefaultOrganizationId(),
            $options
        );
        $users = $response->getUsers();

        if (!$users) {
            return null;
        }

        return $users[0];
    }

    /**
     * Get default permission profile name by employee name
     *
     * @param string $name
     * @return string|null
     */
    public function getDefaultPermissionProfileNameByEmployeeName(string $name): ?string
    {
        $code = null;

        foreach(config('settings.employees') as $user) {
            if ($user['name'] === $name) {
                $code = $user['profile'];
            }
        }

        if ($code) {
            return Arr::get(app(PermissionProfileNamesEnum::class)->getAll(), $code);
        }

        return null;
    }
}