<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Deleting an account is irreversible, so the driver re-proves who they are.
 *
 * Checked inline rather than behind Fortify's password.confirm middleware:
 * Fortify::confirmPasswordView() is never set in this app, so there is no
 * confirmation screen to redirect to. Doing it here also keeps the driver
 * inside the dialog instead of bouncing them to another page and back.
 */
final class DeleteAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'password' => ['required', 'string', 'current_password:web'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'password.current_password' => __('messages.validation.currentPassword'),
        ];
    }
}
