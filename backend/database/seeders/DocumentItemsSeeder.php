<?php

namespace Database\Seeders;

use App\Enums\DocumentItemTypesEnum;
use App\Enums\PermissionProfileNamesEnum;
use App\Models\DocumentItem;
use App\Models\PermissionProfile;
use Illuminate\Database\Seeder;

class DocumentItemsSeeder extends Seeder
{
    /**
     * Document items
     */
    protected const DOCUMENT_ITEMS = [
        [
            'name'                => 'Mouse',
            'type'                => DocumentItemTypesEnum::EQUIPMENT,
            'permission_profiles' => ['admin'],
        ],
        [
            'name'                => 'Keyboard',
            'type'                => DocumentItemTypesEnum::EQUIPMENT,
            'permission_profiles' => ['admin'],
        ],
        [
            'name'                => 'Monitor',
            'type'                => DocumentItemTypesEnum::EQUIPMENT,
            'permission_profiles' => ['admin'],
        ],
        [
            'name'                => 'Computer Tower',
            'type'                => DocumentItemTypesEnum::EQUIPMENT,
            'permission_profiles' => ['admin'],
        ],
        [
            'name'                => 'Laptop',
            'type'                => DocumentItemTypesEnum::EQUIPMENT,
            'permission_profiles' => ['manager', 'employee'],
        ],
        [
            'name'                => 'Tally HR',
            'type'                => DocumentItemTypesEnum::SOFTWARE,
            'permission_profiles' => ['admin'],
        ],
        [
            'name'                => 'Tally Code',
            'type'                => DocumentItemTypesEnum::SOFTWARE,
            'permission_profiles' => ['admin', 'manager', 'employee'],
        ],
        [
            'name'                => 'Tally Manage',
            'type'                => DocumentItemTypesEnum::SOFTWARE,
            'permission_profiles' => ['admin', 'manager'],
        ],
        [
            'name'                => 'Tally Learning',
            'type'                => DocumentItemTypesEnum::SOFTWARE,
            'permission_profiles' => ['admin', 'manager', 'employee'],
        ],
        [
            'name'                => 'Tally Office',
            'type'                => DocumentItemTypesEnum::SOFTWARE,
            'permission_profiles' => ['admin', 'manager', 'employee'],
        ],
        [
            'name'                => 'Tally Mail',
            'type'                => DocumentItemTypesEnum::SOFTWARE,
            'permission_profiles' => ['admin', 'manager', 'employee'],
        ],
    ];

    /**
     * Seed the documents
     *
     * @return void
     */
    public function run()
    {
        $profiles = PermissionProfile::all();

        foreach(self::DOCUMENT_ITEMS as $item) {
            $documentItem = $this->findOrCreateItem($item);
            $names        = $this->getProfileNamesFromCodes($item);
            $profileIds   = $profiles->whereIn('name', $names)
                ->pluck('id')
                ->toArray();
            $documentItem->permissionProfiles()->sync($profileIds);
        }
    }

    /**
     * Get profile names from codes
     *
     * @param array $item
     * @return array
     */
    protected function getProfileNamesFromCodes(array $item): array
    {
        $profileNames = app(PermissionProfileNamesEnum::class)->getAll();

        return array_map(function (string $profileCode) use ($profileNames) {
            return $profileNames[$profileCode];
        }, $item['permission_profiles']);
    }

    /**
     * Find or create item
     *
     * @param array $item
     * @return DocumentItem
     */
    protected function findOrCreateItem(array $item): DocumentItem
    {
        if ($documentItem = DocumentItem::query()->where('name', $item['name'])->first()) {
            return $documentItem;
        }

        return DocumentItem::query()->create([
            'name' => $item['name'],
            'type' => $item['type'],
        ]);
    }
}
