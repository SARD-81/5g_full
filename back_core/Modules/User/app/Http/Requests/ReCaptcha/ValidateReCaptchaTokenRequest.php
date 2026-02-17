<?php

namespace Modules\User\Http\Requests\ReCaptcha;

use Illuminate\Foundation\Http\FormRequest;

class ValidateReCaptchaTokenRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'response' => ['required', 'string'],
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
