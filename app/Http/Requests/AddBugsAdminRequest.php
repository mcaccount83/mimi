<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddBugsAdminRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'taskNameNew' => [
                'required',
                'string',
                'max:255',
            ],
            'taskDetailsNew' => [
                'required',
                'string',
            ],
            'taskPriorityNew' => [
                'required',
            ],
        ];
    }
}
