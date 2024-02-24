<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddProgressionAdminRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
'taskNameNew'     => 'required|string|max:255',
'taskDetailsNew'  => 'required|string',
'taskPriorityNew' => 'required',
];
    }
}
