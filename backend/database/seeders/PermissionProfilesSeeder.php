<?php

namespace Database\Seeders;

use App\Enums\PermissionProfileNamesEnum;
use App\Models\PermissionProfile;
use App\Services\DocuSign\AuthService;
use App\Services\DocuSign\PermissionProfileService;
use DocuSign\Admin\Client\ApiException;
use DocuSign\Admin\Model\PermissionProfileResponse;
use Exception;
use Illuminate\Database\Seeder;

class PermissionProfilesSeeder extends Seeder
{
    /**
     * Seed the documents
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        $profiles = $this->getFromServer();
        $profiles = $this->filterProfiles($profiles);
        $this->checkProfilesCount($profiles);

        foreach($profiles as $profile) {
            if (!$this->existsProfile($profile->getId())) {
                PermissionProfile::query()->create([
                    'name'   => $profile->getName(),
                    'ext_id' => $profile->getId(),
                ]);
            }
        }
    }

    /**
     * Get from server
     *
     * @return PermissionProfileResponse[]
     * @throws ApiException
     */
    protected function getFromServer(): array
    {
        /** @var AuthService $authService */
        $authService = app(AuthService::class);
        $tokenId = $authService->requestToken(config('settings.docusign.user_id'))->getAccessToken();

        $permissionProfileService = app(PermissionProfileService::class, compact('tokenId'));

        return $permissionProfileService->getListFromServer();
    }

    /**
     * Filter profiles
     *
     * @param PermissionProfileResponse[] $profiles
     * @return PermissionProfileResponse[]
     */
    protected function filterProfiles(array $profiles): array
    {
        $names = array_values(app(PermissionProfileNamesEnum::class)->getAll());

        return array_filter($profiles, function (PermissionProfileResponse $profile) use ($names) {
            return in_array($profile->getName(), $names);
        });
    }

    /**
     * Check profiles count from response
     *
     * @param PermissionProfileResponse[] $profiles
     * @return void
     * @throws Exception
     */
    protected function checkProfilesCount(array $profiles)
    {
        $names = array_values(app(PermissionProfileNamesEnum::class)->getAll());

        if (count($names) === count($profiles)) {
            return;
        }

        $profiles = array_map(fn(PermissionProfileResponse $profile) => $profile->getName(), $profiles);
        $result = array_diff($names, $profiles);

        throw new Exception('Profiles ' . implode(', ', $result) . ' are not found in response');
    }

    /**
     * Check if permission profile exists
     *
     * @param string $extId
     * @return bool
     */
    protected function existsProfile(string $extId): bool
    {
        return app(PermissionProfile::class)->query()->where('ext_id', $extId)->exists();
    }
}
