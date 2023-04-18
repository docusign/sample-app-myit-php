<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class DeletePermissionProfileRequest
 *
 * @package App\Http\Requests\Api
 */
class DeletePermissionProfileRequest extends FormRequest
{
    /**
     * Rules of request
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'employees_ids'   => [
                'required',
                'array',
            ],
            'employees_ids.*' => [
                'required',
                Rule::exists('employees', 'id'),
            ],
        ];
    }
}