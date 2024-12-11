<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexMailRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
'status'      => [
'nullable',
'numeric',
Rule::in(MonitorStatus::toArray()),
],
'queue'       => [
'nullable',
'string',
],
'name'        => [
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
