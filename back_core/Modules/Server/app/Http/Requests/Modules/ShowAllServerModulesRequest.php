<?php

namespace Modules\Server\Http\Requests\Modules;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

 class ShowAllServerModulesRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'min:1', 'max:128'],
            'password' => ['required', 'string', 'min:1', 'max:128'],
            'port' => ['nullable', 'integer', 'min:1'],
            'server_id' => [
                'required',
                'numeric',
                'exists:servers,id',
                function ($attribute, $value, $fail) {
                    $server = DB::table('servers')->where('id', $value)->first();
                    if (! $server) {
                        $fail("The selected server ID ($value) is invalid.");
                        return;
                    }

                    if (empty($server->path_config) || empty($server->path_run_config)) {
                        $fail("The selected server ($value) is missing required configuration paths (path_config and path_run_config).");
                    }
                },
            ],
        ];
    }
    
    public function authorize(): bool
    {
        return true;
    }
}