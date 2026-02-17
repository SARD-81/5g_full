<?php

namespace Modules\SystemSetting\Http\Requests\SystemSetting;

use Illuminate\Foundation\Http\FormRequest;

class SetConfigConnectionSMSRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'connection-data' => ['required', 'array']
        ];
    }




    public function withValidator ($validator)
    {
        if ($validator->errors()->any())
            return;


        $validator->after(function ($validator) {

            $requiredKeys = ['username', 'password', 'special_number'];

            foreach ($requiredKeys as $key)
                if (!array_key_exists($key, $this->input('connection-data')))
                    $validator->errors()->add("The key '$key' must exist in the JSON of 'connection_data'.");

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
