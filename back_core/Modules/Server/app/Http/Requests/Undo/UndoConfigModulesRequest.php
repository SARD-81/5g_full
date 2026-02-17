<?php

namespace Modules\Server\Http\Requests\Undo;

use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Modules\Server\Models\Server;

class UndoConfigModulesRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'module_id' => ['required', 'exists:modules,id', 'integer'],

            'server_id' => ['required', 'exists:servers,id', 'integer',  function ($attribute, $value, $fail) {
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

            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
            'port' => ['nullable', 'integer'],
        ];
    }



    public function withValidator ($validator)
    {
        if ($validator->errors()->any())
            return;


        $server = Server::find($this->input('server_id'));
        $module = $server->modules()->where('modules.id', $this->input('module_id'))->first();

        $validator->after(function ($validator) use ($server, $module) {

            if ($server['is_down'] == Server::OFF)
                $validator->errors()->add('server', 'selected server is off');


            $this->merge([
                'server' => $server,
                'module' => $module,
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
