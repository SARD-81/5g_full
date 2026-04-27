<?php

namespace Modules\Server\Http\Requests\Modules;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CreateModulesRequest extends FormRequest
{
    private const ALLOWED_MODULE_TYPES = ['hss', 'pcrf', 'upf', 'sgwc', 'sgwu', 'smf', 'mme'];

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:24',
                'regex:/^[^<>{}\/\~`!@#$%&*()\="\':;؟،]*$/u',
            ],
            'type' => [
                'required',
                'string',
                Rule::in(self::ALLOWED_MODULE_TYPES),
            ],
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
            'type.in' => 'The selected module type is invalid. Allowed values: hss, pcrf, upf, sgwc, sgwu, smf, mme.',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
