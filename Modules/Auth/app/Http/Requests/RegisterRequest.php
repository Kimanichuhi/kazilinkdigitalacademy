<?php

namespace Modules\Auth\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * Mirrors the source's register zod schema field-for-field
 * (app/auth/register/page.tsx, see MIGRATION-INVENTORY.md §5).
 *
 * `not_a_robot` is a plain required checkbox, not a real bot-detection
 * service (no reCAPTCHA/hCaptcha is wired into this app) — it just blocks
 * submission until checked.
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
            'email_notifications_opt_in' => ['sometimes', 'boolean'],
            'not_a_robot' => ['accepted'],
            'accept_terms' => ['accepted'],
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
            'not_a_robot.accepted' => 'Please confirm you are not a robot',
            'accept_terms.accepted' => 'You must accept the Terms & Conditions',
        ];
    }
}
