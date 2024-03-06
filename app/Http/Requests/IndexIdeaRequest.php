<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexIdeaRequest extends FormRequest
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
            'search' => ['string', 'max:255'],
            'perpage' => ['integer', 'min:5', 'max:50'],
            // 'sort' => ['string', 'max:255'],
        ];
    }
}
