<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrchestratorConnectionTenantAlertRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules = [
            'external_id' => 'required',
            'notification_name' => 'required',
            'data' => 'required|json',
            'component' => 'required',
            'severity' => 'required',
            'creation_time' => 'required',
            'deep_link_relative_url' => 'nullable',
            'resolution_details' => 'nullable',
        ];

        return $rules;
    }
}
