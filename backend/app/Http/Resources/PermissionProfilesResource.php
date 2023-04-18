<?php

namespace App\Http\Resources;

use App\Models\PermissionProfile;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class PermissionProfilesResource
 *
 * @mixin PermissionProfile $permissionProfile
 *
 * @package App\Http\Resources
 */
class PermissionProfilesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param $request
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'equipments' => DocumentItemResource::collection($this->equipments),
            'software'   => DocumentItemResource::collection($this->software),
        ];
    }
}