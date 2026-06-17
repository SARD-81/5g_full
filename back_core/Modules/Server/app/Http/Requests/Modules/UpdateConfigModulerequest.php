<?php

namespace Modules\Server\Http\Requests\Modules;

use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Http\FormRequest;

class UpdateConfigModulerequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'module_id' => ['required', 'exists:modules,id', 'integer'],
            'data' => ['required', 'array'],

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

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
