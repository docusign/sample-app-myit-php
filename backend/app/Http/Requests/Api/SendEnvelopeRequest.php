<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class SendEnvelopeRequest
 *
 * @package App\Http\Requests\Api
 */
class SendEnvelopeRequest extends FormRequest
{
    /**
     * Rules of request
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'employees'                   => [
                'required',
                'array',
            ],
            'employees.*.id'              => [
                'required',
                Rule::exists('employees', 'id'),
            ],
            'employees.*.name'            => [
                'nullable',
                'max:255',
            ],
            'employees.*.email'           => [
                'nullable',
                'max:255',
                'email',
            ],
            'employees.*.equipment_ids'   => [
                'required',
                'array',
            ],
            'employees.*.equipment_ids.*' => [
                'required',
                Rule::exists('document_items', 'id'),
            ],
            'employees.*.software_ids'    => [
                'required',
                'array',
            ],
            'employees.*.software_ids.*'  => [
                'required',
                Rule::exists('document_items', 'id'),
            ],
        ];
    }
}