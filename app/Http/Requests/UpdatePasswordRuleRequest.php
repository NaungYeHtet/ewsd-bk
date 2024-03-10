<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordRuleRequest extends FormRequest
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
            'min' => ['required', 'integer', 'min:5', 'max:50'],
            'max' => ['required', 'integer', 'gt:min', 'max:255'],
            'letters' => ['required', 'boolean'],
            'numbers' => ['required', 'boolean'],
            'mixed_case' => ['required', 'boolean'],
            'symbols' => ['required', 'boolean'],
        ];
    }
}
