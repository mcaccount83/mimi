<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBugsAdminRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'taskDetails' => [
                'required',
                'string',
            ],
            'taskNotes' => [
                'nullable',
                'string',
            ],
            'taskStatus' => [
                'required',
            ],
            'taskPriority' => [
                'required',
            ],
        ];
    }
}
