<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProgressionAdminRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
'taskDetails'  => 'required|string',
'taskNotes'    => 'nullable|string',
'taskStatus'   => 'required',
'taskPriority' => 'required',
];
    }
}
