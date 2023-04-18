<?php

namespace App\Http\Resources;

use App\Models\Employee;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class EmployeeResource
 *
 * @mixin Employee $employee
 *
 * @package App\Http\Resources
 */
class EmployeeResource extends JsonResource
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
            'id'                => $this->id,
            'name'              => $this->name,
            'email'             => $this->display_email,
            'permissionProfile' => new PermissionProfilesResource($this->permissionProfile),
            'equipmentsList'    => DocumentItemResource::collection($this->equipments),
            'softwareList'      => DocumentItemResource::collection($this->software),
        ];
    }
}