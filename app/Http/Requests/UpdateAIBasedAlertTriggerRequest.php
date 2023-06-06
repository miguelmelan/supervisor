<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAIBasedAlertTriggerRequest extends FormRequest
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
        return [
            'code' => 'required|min:2|max:5|unique:a_i_based_alert_triggers,name,' . $this->id,
            'name' => 'required|unique:a_i_based_alert_triggers,name,' . $this->id,
            'orchestrator_connections' => 'required|array',
            'tags' => 'array',
            'conditions' => 'required',
            'recurrence' => 'required',
            'crons' => 'required|array',
            'verifications' => 'required',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'orchestrator_connections.required' => __('At least one UiPath Orchestrator connection must be selected.'),
        ];
    }
}
