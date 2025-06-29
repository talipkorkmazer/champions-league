<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request for storing a new league
 */
class StoreLeagueRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'team_ids' => 'required|array|size:4',
            'team_ids.*' => 'exists:teams,id'
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The league name is required.',
            'name.string' => 'The league name must be a string.',
            'name.max' => 'The league name may not be greater than 255 characters.',
            'team_ids.required' => 'Please select teams for the league.',
            'team_ids.array' => 'Teams must be selected as a list.',
            'team_ids.size' => 'Exactly 4 teams must be selected for the league.',
            'team_ids.*.exists' => 'One or more selected teams do not exist.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'league name',
            'team_ids' => 'teams',
            'team_ids.*' => 'team'
        ];
    }
}
