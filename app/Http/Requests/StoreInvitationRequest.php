<?php

namespace App\Http\Requests;

use App\Models\TenantUserAccess;
use App\Support\TenantAccess;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInvitationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (! tenancy()->initialized) {
            return false;
        }

        return TenantAccess::canManage($this->user(), tenant()->getTenantKey());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255'],
            'role' => ['required', 'string', Rule::in(TenantUserAccess::ROLES)],
        ];
    }
}
