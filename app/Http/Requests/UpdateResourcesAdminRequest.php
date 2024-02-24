<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateResourcesAdminRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'fileDescription' => [
                'required',
                'string',
                'max:500',
            ],
'fileType'        => [
                'required',
            ],
'fileVersion'     => [
                'nullable',
                'string',
                'max:25',
            ],
'link'            => [
                'nullable',
                'string',
                'max:255',
            ],
];
    }
}
