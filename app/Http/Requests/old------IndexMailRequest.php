<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use romanzipp\QueueMonitor\Enums\MonitorStatus;

class IndexMailRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'status' => [
                'nullable',
                'numeric',
                Rule::in(MonitorStatus::toArray()),
            ],
            'queue' => [
                'nullable',
                'string',
            ],
            'name' => [
                'nullable',
                'string',
            ],
            'custom_data' => [
                'nullable',
                'string',
            ],
        ];
    }
}
