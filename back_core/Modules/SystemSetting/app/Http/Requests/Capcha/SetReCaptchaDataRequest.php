<?php

namespace Modules\SystemSetting\Http\Requests\Capcha;

use Illuminate\Foundation\Http\FormRequest;

class SetReCaptchaDataRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'recaptcha_secret_key' => ['required', 'string'],
            'recaptcha_site_name' => ['required', 'string'],
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
