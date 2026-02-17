<?php

namespace Modules\User\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class Loginrequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'auth_name' => ['required', 'exists:users,auth_name', 'min:3', 'max:255'],
            'password' => ['required', Password::min(8), 'max:40'],
            'phone' => ['string', 'exists:users,phone'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


}
