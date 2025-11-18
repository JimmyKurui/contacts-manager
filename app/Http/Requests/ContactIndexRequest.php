<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactIndexRequest extends FormRequest
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
            'page' => 'nullable|integer',
            'per_page' => 'nullable|integer|min:5|max:100',
            'q' => 'nullable|string|max:50',
            'company' => 'nullable|string|max:255',
            'sort' => 'nullable|in:name,company,created_at',
            'direction' => 'nullable|in:asc,desc',
        ];
    }
}
