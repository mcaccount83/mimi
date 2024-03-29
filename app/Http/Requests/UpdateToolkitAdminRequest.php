<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateToolkitAdminRequest extends FormRequest
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
                'max:255',
            ],
            'fileType' => [
                'required',
            ],
            'fileVersion' => [
                'nullable',
                'string',
                'max:25',
            ],
            'link' => [
                'nullable',
                'string',
                'max:255',
            ],
            'filePath' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }
}
