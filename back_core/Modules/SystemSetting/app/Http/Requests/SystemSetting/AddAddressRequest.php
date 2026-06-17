<?php

namespace Modules\SystemSetting\Http\Requests\SystemSetting;

use Illuminate\Foundation\Http\FormRequest;
use Modules\SystemSetting\Models\SystemSettings;

class AddAddressRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:1', 'max:255'],
            'url' => ['required', 'string', 'min:1', 'max:255'],
        ];
    }



    public function withValidator ($validator)
    {
        if ($validator->errors()->any())
            return;

            $systemSetting = SystemSettings::first();
        $validator->after(function ($validator) use ($systemSetting) {

            $newMonitoring = [
                'name' => $this->input('name'),
                'url'  => $this->input('url'),
            ];

            //        validated name
            $monitoringList = $systemSetting->monitoring_attribute['monitoring'] ?? [];
            $exists = collect($monitoringList)->contains(fn($item) => $item['name'] === $newMonitoring['name']);
//dd($monitoringList, $exists);
            if ($exists)
                return $validator->errors()->add('monitoring_name', 'The monitoring attribute already exists.');


            $this->merge([
                'newMonitoring' => $newMonitoring,
                'inServiceNameExists' => $exists,
            ]);
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
