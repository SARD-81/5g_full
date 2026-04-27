<?php

namespace Modules\Server\Http\Requests\Module;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\Server\Models\Module;
use Modules\Server\Models\Server;

class restartServiceModuleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'module_id' => ['required', 'integer', 'exists:modules,id'],
            'server_id' => ['required', 'integer', 'exists:servers,id', function ($attribute, $value, $fail) {
                $server = DB::table('servers')->where('id', $value)->first();
                if (!$server) {
                    $fail("The selected server ID ($value) is invalid.");
                    return;
                }

                if (empty($server->path_config) || empty($server->path_run_config)) {
                    $fail("The selected server ($value) is missing required configuration paths (path_config and path_run_config).");
                }
            }],

            'username' => ['required', 'string', 'min:1', 'max:128', 'regex:/^\S+$/'],
            'password' => ['required', 'string', 'min:1', 'max:256'],
            'port' => ['required', 'integer', 'between:1,65535'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $normalizedPort = $this->port;
        if ($normalizedPort === '' || $normalizedPort === null) {
            $normalizedPort = 22;
        }

        $this->merge([
            'username' => is_string($this->username) ? trim($this->username) : $this->username,
            'password' => is_string($this->password) ? trim($this->password) : $this->password,
            'port' => is_numeric($normalizedPort) ? (int) $normalizedPort : $normalizedPort,
        ]);
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $module = Module::find($this->input('module_id'));
            if (!$module) {
                return;
            }

            $isAttached = $module->servers()->where('server_id', $this->input('server_id'))->exists();
            if (!$isAttached) {
                $validator->errors()->add('module_id', 'The selected module is not attached to the selected server.');
            }

            $server = Server::query()->find($this->input('server_id'));
            if (!$server) {
                return;
            }

            if ($server->is_down === Server::OFF) {
                $validator->errors()->add('server_id', "The selected server ({$server->name}) is offline.");
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

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'error_code' => 'validation_failed',
            'message' => 'Validation failed.',
            'details' => $validator->errors()->toArray(),
        ], 422));
    }
}
