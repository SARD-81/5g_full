<?php

namespace Modules\Server\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Modules\Server\Models\Module;
use Modules\Server\Utility\ModuleIdentity;

class EditModuleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'module_id' => ['required', 'integer', 'exists:modules,id'],
            'name' => [
                'nullable',
                'string',
                'min:2',
                'max:24',
                'regex:/^[^<>{}\/|\~`!@#$%&*()\+="\':;؟،]*$/u',
                function ($attribute, $value, $fail) {
                    if ($value === null) {
                        return;
                    }

                    $serviceKey = ModuleIdentity::normalizeKey($value);
                    $moduleId = (int) $this->input('module_id');

                    if (Module::where('service_key', $serviceKey)->where('id', '!=', $moduleId)->exists()) {
                        $fail('module technical identity already exists.');
                    }
                },
            ],
            'type' => ['nullable', 'string', 'regex:/^[^<>{}\/|\~`!@#$%&*()_\-+="\':;؟،]*$/u'],
            'config_file' => ['nullable', 'file', function ($attribute, $value, $fail) {
                if (!preg_match('/\.(yaml|yml|yaml\.in)$/i', $value->getClientOriginalName())) {
                    $fail('The file must be one of the following formats: .yaml, .yml, or .yaml.in');
                }
            }],
            'servers' => ['sometimes', 'array'],
            'servers.*.id' => ['required_with:servers', 'integer', 'distinct', 'exists:servers,id', function ($attribute, $value, $fail) {
                $server = DB::table('servers')->where('id', $value)->first();
                if (!$server) {
                    $fail("The selected server ID ($value) is invalid.");
                    return;
                }

                if (empty($server->path_config) || empty($server->path_run_config)) {
                    $fail("The selected server ($value) is missing required configuration paths (path_config and path_run_config).");
                }
            }],
            'servers.*.username' => ['required_with:servers.*.id', 'string', 'min:1', 'max:255'],
            'servers.*.password' => ['required_with:servers.*.id', 'string', 'min:1', 'max:255'],
            'servers.*.port' => ['required_with:servers.*.id', 'integer'],
            'servers_to_remove' => ['sometimes', 'array'],
            'servers_to_remove.*.id' => ['required_with:servers_to_remove', 'integer', 'distinct', 'exists:servers,id'],
            'servers_to_remove.*.username' => ['required_with:servers_to_remove.*.id', 'string', 'min:1', 'max:255'],
            'servers_to_remove.*.password' => ['required_with:servers_to_remove.*.id', 'string', 'min:1', 'max:255'],
            'servers_to_remove.*.port' => ['required_with:servers_to_remove.*.id', 'integer'],
        ];
    }

    public function withValidator($validator): void
    {
        if ($validator->errors()->any()) {
            return;
        }

        $validator->after(function ($validator) {
            if (!$this->has('servers')) {
                return;
            }

            $module = Module::with('servers:id')->find($this->input('module_id'));
            if (!$module) {
                return;
            }

            $existingIds = $module->servers->pluck('id')->map(fn ($id) => (int) $id)->all();
            $selectedIds = collect($this->input('servers', []))->pluck('id')->map(fn ($id) => (int) $id)->all();
            $removedIds = array_values(array_diff($existingIds, $selectedIds));

            if (empty($removedIds)) {
                return;
            }

            $removePayloadIds = collect($this->input('servers_to_remove', []))
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();

            sort($removedIds);
            sort($removePayloadIds);

            if ($removedIds !== $removePayloadIds) {
                $validator->errors()->add(
                    'servers_to_remove',
                    'SSH credentials for each removed server are required to clean remote artifacts before detaching.'
                );
            }
        });
    }

    public function messages()
    {
        return [
            'name.regex' => 'The name field contains invalid characters. characters like < > { } / | \\ ~ ` ! @ # $ % & * ( ) + = " \' : ; are not allowed.',
            'type.regex' => 'The name field contains invalid characters. characters like < > { } / | \\ ~ ` ! @ # $ % & * ( ) + = " \' : ; are not allowed.',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
