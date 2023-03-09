<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrchestratorConnectionRequest extends FormRequest
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
            'code' => 'required|min:2|max:5|unique:orchestrator_connections',
            'name' => 'required|unique:orchestrator_connections',
            'hosting_type' => 'required|in:on_premise,cloud',
            'environment_type' => 'required|in:development,test,production,hybrid',
            'tenants' => 'required|array',
            'elasticsearch_enabled' => 'boolean',
            'kibana_enabled' => 'boolean',
            'tags' => 'array',
        ];
        
        if ($this->hosting_type === 'on_premise') {
            $rules = array_merge($rules, [
                'url' => 'required|url|unique:orchestrator_connections',
                'tenants.*.id' => 'nullable',
                'tenants.*.name' => 'required',
                'tenants.*.client_id' => 'required',
                'tenants.*.client_secret' => 'required',
            ]);
        } elseif ($this->hosting_type === 'cloud') {
            $rules = array_merge($rules, [
                'organization_name' => 'required|unique:orchestrator_connections',
                'client_id' => 'required',
                'client_secret' => 'required',
            ]);
        }

        if ($this->elasticsearch_enabled) {
            $rules = array_merge($rules, [
                'elasticsearch_index_configuration' => 'required',
                'elasticsearch_url' => 'required|url',
                'elasticsearch_anonymous_authentication' => 'boolean',
            ]);

            if (!$this->elasticsearch_anonymous_authentication) {
                $rules = array_merge($rules, [
                    'elasticsearch_username' => 'required',
                    'elasticsearch_password' => 'required',
                ]);
            } else {
                $rules = array_merge($rules, [
                    'elasticsearch_username' => 'nullable',
                    'elasticsearch_password' => 'nullable',
                ]);
            }
        }

        if ($this->kibana_enabled) {
            $rules = array_merge($rules, [
                'kibana_url' => 'required|url',
            ]);
        }
        
        return $rules;
    }
}
