<?php

namespace App\Http\Requests;

use App\Support\TenantAccess;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class VerifyVendorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return tenancy()->initialized
            && TenantAccess::canManage($this->user(), tenant()->getTenantKey());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'verified_phone' => ['required', 'string', 'max:20', StoreVendorRequest::PHONE_RULE],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'verified_phone.regex' => 'The verified phone must be a valid E.164 number (e.g. +15551234567).',
        ];
    }
}
