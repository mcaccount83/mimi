<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Store990NGoogleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return ['990n' => [
                'required',
            ],];
    }
}
