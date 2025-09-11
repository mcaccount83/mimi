<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateConfListAdminRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
'id'                     => [
                'required',
                'exists:conference,id',
            ],
'conference_name'        => [
                'required',
                'string',
                'max:255',
            ],
'short_name'             => [
                'required',
                'string',
                'max:50',
            ],
'conference_description' => [
                'required',
                'string',
                'max:500',
            ],
'short_description'      => [
                'required',
                'string',
                'max:10',
            ],
];
    }
}
