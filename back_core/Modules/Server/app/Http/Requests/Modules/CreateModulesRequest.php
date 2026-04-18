<?php

namespace Modules\Server\Http\Requests\Modules;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Modules\Server\Models\Module;
use Modules\Server\Utility\ModuleIdentity;

class CreateModulesRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:24',
                'regex:/^[^<>{}\/\~`!@#$%&*()\="\':;؟،]*$/u',
                function ($attribute, $value, $fail) {
                    $serviceKey = ModuleIdentity::normalizeKey($value);
                    if (Module::where('service_key', $serviceKey)->exists()) {
                        $fail('module technical identity already exists.');
                    }
                },
            ],
            'type' => ['required', 'string', 'min:2', 'max:255', 'regex:/^[^<>{}\/|\~`!@#$%&*()_\-+="\':;؟،]*$/u'],
            'config_file' => ['required', 'file', function ($attribute, $value, $fail) {
                if (!preg_match('/\.(yaml|yml|yaml\.in|json|conf|conf\.in)$/i', $value->getClientOriginalName())) {
                    $fail('The file must be one of the following formats: .yaml, .yml, .yaml.in, .json, .conf, .conf.in');
                }
            }],
            'servers' => ['required', 'array', 'min:1'],
            'servers.*.id' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('servers', 'id'),
                function ($attribute, $value, $fail) {
                    $server = DB::table('servers')->where('id', $value)->first();
                    if (!$server) {
                        $fail("The selected server ID ($value) is invalid.");
                        return;
                    }

                    if (empty($server->path_config) || empty($server->path_run_config)) {
                        $fail("The selected server ($value) is missing required configuration paths (path_config and path_run_config).");
                    }
                },
            ],
            'servers.*.username' => ['required', 'string'],
            'servers.*.password' => ['required', 'string'],
            'servers.*.port' => ['integer'],
        ];
    }

    public function messages()
    {
        return [
            'name.regex' => 'The name field contains invalid characters. characters like < > { } / | \\ ~ ` ! @ # $ % & * ( ) _ - + = " \' : ; are not allowed.',
            'type.regex' => 'The name field contains invalid characters. characters like < > { } / | \\ ~ ` ! @ # $ % & * ( ) _ - + = " \' : ; are not allowed.',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
