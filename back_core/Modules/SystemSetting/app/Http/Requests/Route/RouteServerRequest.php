<?php

namespace Modules\SystemSetting\Http\Requests\Route;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Server\Models\Server;

class RouteServerRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'server_id' => ['required', 'integer', 'exists:servers,id'],
            'username'  => ['required', 'string', 'max:255', 'min:1'],
            'password'  => ['required', 'string', 'max:255', 'min:1'],
            'port'      => ['numeric'],
        ];
    }



    public function withValidator($validator)
    {
        if ($validator->errors()->any())
            return;


            $server = Server::find($this->input('server_id'));
        $validator->after(function ($validator) use ($server) {

            if (!$server['ip'])
                return $validator->errors()->add('validation', 'selected server is not ip address.');

            if ($server['is_down'])
                return $validator->errors()->add('validation', 'selected server is down.');


            $this->merge(['server' => $server]);
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
