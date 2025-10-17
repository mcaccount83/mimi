<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEOYRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'reset_eoy_tables' => [
                'nullable',
            ],
            'display_testing' => [
                'nullable',
            ],
            'display_live' => [
                'nullable',
            ],
            'update_user_tables' => [
                'nullable',
            ],
            'subscribe_list' => [
                'nullable',
            ],
            'unsubscribe_list' => [
                'nullable',
            ],
            'reset_AFTER_testing' => [
                'nullable',
            ],
            // Add validation rules for other fields here
        ];
    }
}
