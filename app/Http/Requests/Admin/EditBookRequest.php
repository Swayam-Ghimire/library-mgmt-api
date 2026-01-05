<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class EditBookRequest extends FormRequest
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
            'isbn' => 'prohibited',
            'title' => 'sometimes|required|string',
            'author' => 'sometimes|required|string',
            'genre' => 'sometimes|nullable|string',
            'description' => 'sometimes|required|min:10|string',
            'quantity' => 'sometimes|numeric'
        ];
    }

    public function messages(): array {
        return [
            'isbn.prohibited' => 'The isbn can only be set during the time of creation of the book.',
        ];
    }
}
