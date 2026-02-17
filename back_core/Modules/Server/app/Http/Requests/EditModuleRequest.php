<?php

namespace Modules\Server\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class EditModuleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'module_id' => ['required', 'integer', 'exists:modules,id'],
            'name' => ['nullable', 'string', 'min:2', 'max:24', 'regex:/^[^<>{}\/|\~`!@#$%&*()\+="\':;؟،]*$/u'],
            'type' => ['nullable', 'string', 'regex:/^[^<>{}\/|\~`!@#$%&*()_\-+="\':;؟،]*$/u'],
            // 'server_id'=> ['required', 'integer','exists:servers,id'],

            'config_file' => ['nullable', 'file',  function ($attribute, $value, $fail) {

                    if (!preg_match('/\.(yaml|yml|yaml\.in)$/i', $value->getClientOriginalName())) {
                        $fail('The file must be one of the following formats: .yaml, .yml, or .yaml.in');
                            return;
                    }
                },
            ],

            'servers'            => ['nullable', 'array'],
            'servers.*.id'       => ['required', 'integer', 'exists:servers,id',  function ($attribute, $value, $fail) {
                $server = DB::table('servers')->where('id', $value)->first();
                    if (!$server) {
                        $fail("The selected server ID ($value) is invalid.");
                        return;
                    }

                    if (empty($server->path_config) || empty($server->path_run_config)) {
                        $fail("The selected server ($value) is missing required configuration paths (path_config and path_run_config).");
                        return;
                    }
                }
            ],
            'servers.*.username' => ['required', 'string', 'min:1', 'max:255'],
            'servers.*.password' => ['required', 'string', 'min:1', 'max:255'],
            'servers.*.port'     => ['nullable', 'integer']
        ];
    }



    public function messages()
    {
        return [
            'name.regex' => 'The name field contains invalid characters. characters like < > { } / | \ ~ ` ! @ # $ % & * ( ) + = " \' : ; are not allowed.',
            'type.regex' => 'The name field contains invalid characters. characters like < > { } / | \ ~ ` ! @ # $ % & * ( ) + = " \' : ; are not allowed.',
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
