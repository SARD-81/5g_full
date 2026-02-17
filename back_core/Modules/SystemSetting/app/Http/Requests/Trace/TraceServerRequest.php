<?php

namespace Modules\SystemSetting\Http\Requests\Trace;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Server\Models\Server;
use Modules\Server\Utility\AppUtility;

class TraceServerRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'current_server_password' => [
                request()->routeIs('trace-server-stop') ? 'required' : 'nullable',
                'string'
            ],

            'servers'            => ['required', 'array'],
            'servers.*.id'       => ['required', 'integer', 'exists:servers,id'],
            'servers.*.username' => ['required', 'string', 'min:1'],
            'servers.*.password' => ['required', 'string', 'min:1'],
            'servers.*.port'     => ['integer'],

            'servers.*.module_ids'        => ['array'],
            'servers.*.module_ids.*'      => ['required', 'integer', 'exists:modules,id'],
            'servers.*.interface'         => ['array'],
            'servers.*.module_identifier' => ['string']
        ];
    }




    public function withValidator ($validator)
    {
        if ($validator->errors()->any()) return;

        $serverIds = collect($this->input('servers'))->pluck('id')->toArray();
        $servers   = Server::whereIn('id', $serverIds)->get();

        $validator->after(function ($validator) use ($servers) {

            if (!AppUtility::validatePasswordCurrentServer($this->input('current_server_password')))
                return $validator->errors()->add('current_server_password', 'Your current password is incorrect.');

            foreach ($servers as $server) {
                if (!$server['ip']) return $validator->errors()->add('validation', 'selected server is not ip address.');

                if ($server['is_down']) return $validator->errors()->add('validation', 'selected server is down.');
            }

            $this->merge(['servers' => $servers]);
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
