<?php

namespace Modules\Server\Http\Requests\Module;

use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Http\FormRequest;

class ExpertModuleFileIsServerRequset extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'module_id' => ['required', 'integer', 'exists:modules,id',],

            'server_id' => ['required', 'integer', 'exists:servers,id', function ($attribute, $value, $fail) {
                $server = DB::table('servers')->find($value);
                if (!$server) {
                    $fail("The selected server ID ($value) is invalid.");
                    return;
                }

                if (empty($server->path_config) || empty($server->path_run_config)) {
                    $fail("The selected server ($value) is missing required configuration paths (path_config and path_run_config).");
                    return;
                }
            }],
            'username' => ['required', 'string', 'min:1', 'max:255'],
            'password' => ['required', 'string', 'min:1', 'max:255'],
            'port' => ['nullable', 'integer']
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
