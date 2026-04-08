<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Libxa\Http\FormRequest;

class ExcellenceTestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Simple true for testing the suite
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'test_title'   => 'required|min:5',
            'test_content' => 'required|min:10',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'test_title.required' => 'The test title is strictly required to verify form requests.',
            'test_title.min'      => 'Validation check: Title must be at least 5 characters.',
            'test_content.min'    => 'Validation check: Content must be at least 10 characters long.',
        ];
    }
}
