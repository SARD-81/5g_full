<?php

namespace Modules\Server\Http\Requests\Server;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class EditServerReqest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'server_id' => ['required', 'exists:servers,id'],

            'name' => [
                'string',
                'min:2',
                'max:127',
                Rule::unique('servers', 'name')->ignore($this->server_id, 'id'),
                'regex:/^[^<>{}\/|\~`!@#$%&*()\+="\':;؟،]*$/u'
            ],


            'ip' => [
                'nullable',
                Rule::unique('servers', 'ip')->ignore($this->server_id, 'id'),
                'regex:/^((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/'
            ],

            'path_config' => ['nullable', 'string',  'min:1', 'max:1024',  'regex:/^\/(?:[a-zA-Z0-9_\-\.]+\/)*[a-zA-Z0-9_\-\.]+\/$/'],
            'path_run_config' => ['nullable', 'string',  'min:1', 'max:1024',  'regex:/^\/(?:[a-zA-Z0-9_\-\.]+\/)*[a-zA-Z0-9_\-\.]+\/$/'],
        ];
    }


    public function messagees()
    {
        return [
            'name.regex' => 'The name field contains invalid characters. characters like < > { } / | \ ~ ` ! @ # $ % & * ( ) _ - + = " \' : ; are not allowed.',


               // Custom messages for general rules
            'path_config.required' => 'The config path is required.',
            'path_config.string' => 'The config path must be a string.',
            'path_config.min' => 'The config path must be at least 3 characters.',
            'path_config.max' => 'The config path may not be longer than 255 characters.',
            'path_config.regex' => 'The config path must be a valid absolute path ending with a slash (e.g., "/home/user/").',
            'path_config.not_regex' => 'The config path cannot be a restricted system directory (e.g., "/bin/", "/etc/").',

            'path_run_config.required' => 'The run config path is required.',
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
