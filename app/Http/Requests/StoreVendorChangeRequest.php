<?php

namespace App\Http\Requests;

use App\Models\VendorChangeLog;
use App\Support\TenantAccess;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVendorChangeRequest extends FormRequest
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
            'field_changed' => ['required', 'string', 'max:50'],
            'old_value_hash' => ['nullable', 'string', 'max:64'],
            'new_value_hash' => ['nullable', 'string', 'max:64'],
            'source' => ['sometimes', 'string', Rule::in(VendorChangeLog::SOURCES)],
            'raw_source_ref' => ['nullable', 'string', 'max:255'],
        ];
    }
}
