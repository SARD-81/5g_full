<?php

namespace Modules\Server\Http\Requests\Module;

use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Http\FormRequest;

class DeleteCofigModuleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'module_id' => ['required', 'integer', 'exists:modules,id'],

            'server_id' => ['required', 'integer', 'exists:servers,id',  function ($attribute, $value, $fail) {
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

            'path_config' => ['required', 'array'],
            'path_config.*' => ['required', 'string'],

            'username' => ['required', 'string', 'max:127'],
            'password' => ['required', 'string', 'max:127'],
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
