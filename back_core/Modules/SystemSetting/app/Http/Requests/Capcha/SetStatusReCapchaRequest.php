<?php

namespace Modules\SystemSetting\Http\Requests\Capcha;


use Illuminate\Foundation\Http\FormRequest;
use Modules\SystemSetting\Service\ConnectionTestPublicDomainService;

class SetStatusReCapchaRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'active_online_capcha' => ['required', 'boolean'],
        ];
    }



    public function withValidator ($validator)
    {
        if ($validator->errors()->any())
            return;


        $validator->after(function ($validator) {

            if (! ConnectionTestPublicDomainService::testConnection('8.8.8.8'))
                return $validator->errors()->add('network-connection', 'Connection to the external network is not possible.');
        });
    }




    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
