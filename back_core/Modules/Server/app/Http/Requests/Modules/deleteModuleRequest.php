<?php

namespace Modules\Server\Http\Requests\Modules;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Server\Models\Module;

class deleteModuleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'module_id' => ['required', 'integer', 'exists:modules,id'],
            'servers' => ['sometimes', 'array'],
            'servers.*.id' => ['required_with:servers', 'integer', 'exists:servers,id', 'distinct'],
            'servers.*.username' => ['required_with:servers.*.id', 'string'],
            'servers.*.password' => ['required_with:servers.*.id', 'string'],
            'servers.*.port' => ['nullable', 'integer'],
        ];
    }

    public function withValidator($validator): void
    {
        if ($validator->errors()->any()) {
            return;
        }

        $validator->after(function ($validator) {
            $module = Module::with('servers:id')->find($this->input('module_id'));
            if (!$module || $module->servers->isEmpty()) {
                return;
            }

            $providedIds = collect($this->input('servers', []))->pluck('id')->map(fn ($id) => (int) $id)->all();
            $requiredIds = $module->servers->pluck('id')->map(fn ($id) => (int) $id)->all();
            sort($providedIds);
            sort($requiredIds);

            if ($providedIds !== $requiredIds) {
                $validator->errors()->add('servers', 'SSH credentials for all attached servers are required to clean up remote artifacts before deleting the module.');
            }
        });
    }

    public function authorize(): bool
    {
        return true;
    }
}
