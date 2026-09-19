<?php

namespace App\Http\Requests;

use App\Models\Vendor;
use App\Support\TenantAccess;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVendorRequest extends FormRequest
{
    public const PHONE_RULE = 'regex:/^\+[1-9]\d{6,14}$/';

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
            'name' => ['required', 'string', 'max:255'],
            'provider' => ['sometimes', 'string', Rule::in(Vendor::PROVIDERS)],
            'external_id' => [
                'nullable', 'string', 'max:255',
                Rule::unique('vendors', 'external_id')->where(
                    fn ($query) => $query
                        ->where('tenant_id', tenant()->getTenantKey())
                        ->where('provider', $this->input('provider', 'manual'))
                ),
            ],
            'verified_phone' => ['nullable', 'string', 'max:20', self::PHONE_RULE],
            'bank_account' => ['nullable', 'string', 'max:34'],
            'routing_number' => ['nullable', 'string', 'max:20'],
            'invoice_note' => ['nullable', 'string', 'max:2000'],
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
