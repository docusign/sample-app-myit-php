<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class SendPermissionProfileRequest
 *
 * @package App\Http\Requests\Api
 */
class SendPermissionProfileRequest extends FormRequest
{
    /**
     * Rules of request
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'employees'                         => [
                'required',
                'array',
            ],
            'employees.*.id'                    => [
                'required',
                Rule::exists('employees', 'id'),
            ],
            'employees.*.permission_profile_id' => [
                'required',
                Rule::exists('permission_profiles', 'id'),
            ],
        ];
    }
}