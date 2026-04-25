<?php

namespace Modules\Server\Http\Requests\Modules;

use Attribute;
use Modules\Server\Models\Module;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class CreateModulesRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:24', 'regex:/^[^<>{}\/\~`!@#$%&*()\="\':;؟،]*$/u', function ($attribute, $value, $fail) {
                $serverIds = request('server_id');
                if (!is_array($serverIds))
                    $serverIds = [$serverIds];

                foreach ($serverIds as $serverId) {

                $modulNameExists = Module::where('name', $value)
                ->whereHas('servers', function($query) use ($serverId) {
                    $query->where('servers.id', $serverId);
                })->exists();

                if ($modulNameExists)
                    return $fail('module name is not uniqe in server');
                }

            }],
            'type' => ['required', 'string', 'min:2', 'max:255', 'regex:/^[^<>{}\/|\~`!@#$%&*()_\-+="\':;؟،]*$/u'],
            'config_file' => ['required', 'file',  function ($attribute, $value, $fail) {
if (!preg_match('/\.(yaml|yml|yaml\.in|json|conf|conf\.in)$/i', $value->getClientOriginalName())) {
    $fail('The file must be one of the following formats: .yaml, .yml, .yaml.in, .json, .conf, .conf.in');
    return;
}
            }],


            'servers'            => ['required', 'array'],
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
            'servers.*.username' => ['required', 'string'],
            'servers.*.password' => ['required', 'string'],
            'servers.*.port'     => ['integer'],

        ];
    }




    public function messages()
    {
        return [
           'name.regex' => 'The name field contains invalid characters. characters like < > { } / | \ ~ ` ! @ # $ % & * ( ) _ - + = " \' : ; are not allowed.',
           'type.regex' => 'The name field contains invalid characters. characters like < > { } / | \ ~ ` ! @ # $ % & * ( ) _ - + = " \' : ; are not allowed.',
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
