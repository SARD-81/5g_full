<?php

namespace Modules\Server\Http\Requests\Modules;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Server\Models\Module;

class deleteModuleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'module_id' => ['required', 'integer', 'exists:modules,id'],
            'servers' => ['required', 'array', 'min:1'],
            'servers.*.id' => ['required', 'integer', 'exists:servers,id'],
            'servers.*.username' => ['required', 'string', 'min:1'],
            'servers.*.password' => ['required', 'string', 'min:1'],
            'servers.*.port' => ['nullable', 'integer', 'min:1', 'max:65535'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $moduleId = $this->input('module_id');
            $requestedServers = collect($this->input('servers', []));

            if (!$moduleId || $requestedServers->isEmpty()) {
                return;
            }

            $module = Module::query()->with('servers:id')->find($moduleId);
            if (!$module) {
                return;
            }

            $attachedServerIds = $module->servers->pluck('id')->map(fn ($id) => (int) $id)->sort()->values()->all();
            $requestedServerIds = $requestedServers
                ->pluck('id')
                ->filter(fn ($id) => $id !== null)
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->sort()
                ->values()
                ->all();

            if ($attachedServerIds !== $requestedServerIds) {
                $validator->errors()->add(
                    'servers',
                    'Credentials must be provided for all servers attached to the selected module.'
                );
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
