<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddEmailCampaignAdminReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'campaign'     => 'required|string|max:255|unique:email_campaigns,campaign',
            'label'        => 'nullable|string|max:255',
            'month'        => 'nullable|integer|min:1|max:12',
            'route_name'   => 'required|string|max:255',
            'preview_slug' => 'nullable|string|max:255',
            'attachments'  => 'nullable|string|max:255',
            'active'       => 'boolean',
        ];
    }
}
