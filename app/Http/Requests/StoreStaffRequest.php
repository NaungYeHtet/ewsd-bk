<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreStaffRequest extends FormRequest
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
            'name' => ['required', 'string', 'min:5', 'max:255'],
            'email' => ['required', 'string', 'email', 'unique:staffs'],
            'role' => ['required', 'string', 'exists:roles,name'],
            'department' => ['required', 'string', 'exists:departments,slug'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
            'avatar' => ['image', 'mimes:png,jpg,jpeg,jfif'],
        ];
    }
}
