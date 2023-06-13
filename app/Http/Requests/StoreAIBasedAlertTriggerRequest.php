<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAIBasedAlertTriggerRequest extends FormRequest
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
            'code' => 'required|min:2|max:5|unique:a_i_based_alert_triggers',
            'name' => 'required|unique:a_i_based_alert_triggers',
            'type' => 'required|in:orchestrator_connections,automated_processes',
            'tags' => 'array',
            'conditions' => 'required',
            'recurrence' => 'required',
            'crons' => 'required|array',
            'verifications' => 'required',
        ];

        if ($this->type === 'orchestrator_connections') {
            $rules = array_merge($rules, [
                'orchestrator_connections' => 'required|array',
            ]);
        } elseif ($this->type === 'automated_processes') {
            $rules = array_merge($rules, [
                'automated_processes' => 'required|array',
            ]);
        }

        return $rules;
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
            'automated_processes.required' => __('At least one automated process must be selected.'),
        ];
    }
}
