<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordBoardRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
'current_password' => ['required'],
'new_password'     => [
'required',
'string',
'min:8',
'confirmed',
],
];
    }
}
