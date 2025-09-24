<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreContactRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('contacts', 'email')
                    ->where('user_id', auth()->id())
                    ->whereNull('deleted_at')
            ],
            'mobile_phone' => ['nullable', 'string', 'max:20', 'regex:/^[\d\s\-\+\(\)]+$/'],
            'work_phone' => ['nullable', 'string', 'max:20', 'regex:/^[\d\s\-\+\(\)]+$/'],
            'company' => ['nullable', 'string', 'max:255'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'tags' => ['nullable', 'array', 'max:10'],
            'birthday' => ['nullable', 'date', 'before:today'],
            'avatar' => ['nullable', 'image', 'max:2048', 'mimes:jpg,jpeg,png,gif'],
            'groups' => ['nullable', 'array'],
            'groups.*' => ['integer', 'exists:groups,id'],
            'address' => ['nullable', 'string', 'max:255'],
        ];
    }
}
