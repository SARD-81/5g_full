<?php

namespace Modules\Server\Http\Requests\Server;

use Illuminate\Foundation\Http\FormRequest;

class CreateServerRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'unique:servers,name', 'min:3', 'max:255', 'regex:/^[^<>{}\/|\~`!@#$%&*()\+="\':;؟،]*$/u'],
            'ip' => ['required', 'unique:servers,ip', 'regex:/^((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/'],

            'path_config' => ['nullable', 'string',  'min:1', 'max:1024', 'regex:/^\/(?:[a-zA-Z0-9_\-\.]+\/)*[a-zA-Z0-9_\-\.]+\/$/'],
            'path_run_config' => ['nullable', 'string',  'min:1', 'max:1024', 'regex:/^\/(?:[a-zA-Z0-9_\-\.]+\/)*[a-zA-Z0-9_\-\.]+\/$/',],
        ];
    }

    public function messages()
    {
        return [
            'ip.required' => 'Entering the IP address is mandatory.',
            'ip.unique' => 'The entered IP address has already been registered.',
            'ip.regex' => 'The entered IP address is not valid.',
            'name.regex' => 'The name field contains invalid characters. characters like < > { } / | \ ~ ` ! @ # $ % & * ( ) _ - + = " \' : ; are not allowed.',


            // Custom messages for general rules
//            'path_config.nullable' => 'The config path is required.',
            'path_config.string' => 'The config path must be a string.',
            'path_config.min' => 'The config path must be at least 3 characters.',
            'path_config.max' => 'The config path may not be longer than 255 characters.',
            'path_config.regex' => 'The config path must be a valid absolute path ending with a slash (e.g., "/home/user/").',
            'path_config.not_regex' => 'The config path cannot be a restricted system directory (e.g., "/bin/", "/etc/").',

//            'path_run_config.nullable' => 'The run config path is required.',
            'path_run_config.string' => 'The run config path must be a string.',
            'path_run_config.min' => 'The run config path must be at least 3 characters.',
            'path_run_config.max' => 'The run config path may not be longer than 255 characters.',
            'path_run_config.regex' => 'The run config path must be a valid absolute path ending with a slash (e.g., "/home/user/").',
            'path_run_config.not_regex' => 'The run config path cannot be a restricted system directory (e.g., "/bin/", "/etc/").',
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
