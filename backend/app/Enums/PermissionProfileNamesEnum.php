<?php

namespace App\Enums;

/**
 * Class PermissionProfileNamesEnum
 *
 * @package App\Enums
 */
class PermissionProfileNamesEnum
{
    /**
     * Admin profile name
     *
     * @return string
     */
    public static function admin(): string
    {
        return env('DOCUSIGN_ADMIN_PERMISSION_PROFILE_NAME');
    }

    /**
     * Manager profile name
     *
     * @return string
     */
    public static function manager(): string
    {
        return env('DOCUSIGN_MANAGER_PERMISSION_PROFILE_NAME');
    }

    /**
     * Employee profile name
     *
     * @return string
     */
    public static function employee(): string
    {
        return env('DOCUSIGN_EMPLOYEE_PERMISSION_PROFILE_NAME');
    }

    /**
     * Get all
     *
     * @return string[]
     */
    public function getAll(): array
    {
        return [
            'admin'    => self::admin(),
            'manager'  => self::manager(),
            'employee' => self::employee(),
        ];
    }
}