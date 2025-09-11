<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreConfAdminRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'conference_name' => [
                'required',
                'string',
                'max:255',
            ],
            'short_name' => [
                'required',
                'string',
                'max:50',
            ],
            'conference_description' => [
                'required',
                'string',
                'max:500',
            ],
            'short_description' => [
                'required',
                'string',
                'max:10',
            ],
        ];
    }
}
