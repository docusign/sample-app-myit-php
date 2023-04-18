<?php

namespace App\Mappers;

use Generator;
use Illuminate\Support\Collection;

/**
 * Class DownloadUsersMapper
 *
 * @package App\Mappers
 */
class DownloadUsersMapper
{
    /**
     * Map users
     *
     * @param Collection $employees
     * @return Generator
     */
    public function map(Collection $employees): Generator
    {
        foreach($employees as $employee) {
            yield [
                'name'               => $employee->name,
                'email'              => $employee->email,
                'permission_profile' => $employee->permissionProfile->name,
                'equipments'         => $employee->equipments->implode('name', '; '),
                'software'           => $employee->software->implode('name', '; '),
            ];
        }
    }
}