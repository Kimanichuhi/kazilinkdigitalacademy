<?php

namespace Modules\Auth\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * Mirrors the source's register zod schema field-for-field
 * (app/auth/register/page.tsx, see MIGRATION-INVENTORY.md §5).
 */
class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'min:2'],
            'email' => ['required', 'string', 'email', Rule::unique(User::class, 'email')],
            'phone' => ['required', 'string', 'min:9'],
            'password' => ['required', 'string', Password::defaults()],
            'confirm_password' => ['required', 'same:password'],
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required' => 'Full name required',
            'full_name.min' => 'Full name required',
            'email.required' => 'Valid email required',
            'email.email' => 'Valid email required',
            'phone.required' => 'Valid phone required',
            'phone.min' => 'Valid phone required',
            'password.required' => 'Password is required',
            'confirm_password.same' => 'Passwords do not match',
        ];
    }
}
