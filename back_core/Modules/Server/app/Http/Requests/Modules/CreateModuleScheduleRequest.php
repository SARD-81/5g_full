<?php

namespace Modules\Server\Http\Requests\Modules;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Modules\Server\Helpers\SshHelper;
use Modules\Server\Models\Server;

class CreateModuleScheduleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'module_id'       => ['required', 'integer', 'exists:modules,id'],

            'config_file'     => ['required', 'file',  function ($attribute, $value, $fail) {

if (!preg_match('/\.(yaml|yml|yaml\.in|json|conf|conf\.in)$/i', $value->getClientOriginalName())) {
    $fail('The file must be one of the following formats: .yaml, .yml, .yaml.in, .json, .conf, .conf.in');
    return;
}

            }],

            'run_scheduled_at' => ['required', 'date_format:Y-m-d H:i', 'after:' . now()],
            'password'         => ['required', 'string'],


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
            }],
            'servers.*.username' => ['required', 'string'],
            'servers.*.password' => ['required', 'string'],
            'servers.*.port'     => ['integer'],
        ];
    }


    public function withValidator($validator)
    {
        if ($validator->errors()->any())
            return;

        $serverIds   = array_column($this->input('servers'), 'id');

        $validator->after(function ($validator) use ($serverIds){


            foreach ($serverIds as $serverId) {
                $server      = Server::find($serverId);
                $serverIndex = array_search($serverId, array_column($this->input('servers'), 'id'));
                $username    = $this->input('servers')[$serverIndex]['username'];
                $password    = $this->input('servers')[$serverIndex]['password'];
                $port        = $this->input('servers')[$serverIndex]['port'] ?? 22;

//                if (env('SSH_STATUS')) {
                    $sshHelper = new sshHelper(
                        $server->ip,
                        $username,
                        $password,
                        $port ?? 22,
                        7
                    );

                    $sshHelper->testConnection();
//                }
            }
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
