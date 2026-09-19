<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Services\IncidentService;
use App\Support\TenantAccess;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class TransitionIncidentRequest extends FormRequest
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
            'action' => ['required', 'string', Rule::in(array_keys(IncidentService::TRANSITIONS))],
            'resolution_note' => ['nullable', 'string', 'max:2000', 'required_if:action,blocked,dismissed'],
            'assigned_to_user_id' => ['nullable', 'integer', Rule::exists('users', 'id')],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'resolution_note.required_if' => 'A resolution note is required when blocking or dismissing an incident.',
        ];
    }

    /**
     * Cross-tenant guard for the assignee: the assigned user must belong to
     * (or be granted) the current tenant.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $assigneeId = $this->input('assigned_to_user_id');

            if ($assigneeId === null) {
                return;
            }

            $assignee = User::withoutTenancy()->find($assigneeId);

            if ($assignee === null
                || ! TenantAccess::canAccess($assignee, tenant()->getTenantKey())) {
                $validator->errors()->add('assigned_to_user_id', 'The assignee must be a member of this tenant.');
            }
        });
    }
}
