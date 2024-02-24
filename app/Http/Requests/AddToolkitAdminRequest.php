<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddToolkitAdminRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
'fileCategoryNew'    => 'required',
'fileNameNew'        => 'required|string|max:50',
'fileDescriptionNew' => 'required|string|max:255',
'fileTypeNew'        => 'required',
'fileVersionNew'     => 'nullable|string|max:25',
'LinkNew'            => 'nullable|string|max:255',
'filePathNew'        => 'nullable|string|max:255',
];
    }
}
