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
            // 'fiscal_year' => [
            //     'required',
            //     'string',
            // ],
            'eoy_testers' => [
                'nullable',
            ],
            'eoy_coordinators' => [
                'nullable',
            ],
            'eoy_boardreport' => [
                'nullable',
            ],
            'eoy_financialreport' => [
                'nullable',
            ],
            'truncate_incoming' => [
                'nullable',
            ],
            'truncate_outgoing' => [
                'nullable',
            ],
            'copy_FRtoCH' => [
                'nullable',
            ],
            'copy_CHtoFR' => [
                'nullable',
            ],
            'copy_financial' => [
                'nullable',
            ],
            'copy_chapters' => [
                'nullable',
            ],
            'copy_users' => [
                'nullable',
            ],
            'copy_boarddetails' => [
                'nullable',
            ],
            'copy_coordinatordetails' => [
                'nullable',
            ],
            // Add validation rules for other fields here
        ];
    }
}
