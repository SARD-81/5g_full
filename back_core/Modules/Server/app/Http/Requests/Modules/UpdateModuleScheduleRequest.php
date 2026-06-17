<?php

namespace Modules\Server\Http\Requests\Modules;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Modules\Server\Helpers\SshHelper;
use Modules\Server\Models\ModuleSchedule;
use Modules\Server\Service\Schedule\ModuleScheduleService;

class UpdateModuleScheduleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'module_id'       => ['integer', 'exists:modules,id'],
            'server_id'       => ['integer', 'exists:servers,id'],
            'run_scheduled_at' => ['date_format:Y-m-d H:i', 'after:' . now()],

            'config_file'     => ['file',  function ($attribute, $value, $fail) {

                if (!preg_match('/\.(yaml|yml|yaml\.in)$/i', $value->getClientOriginalName())) {
                    $fail('The file must be one of the following formats: .yaml, .yml, or .yaml.in');
                    return;
                }
            }],

            'username_ssh'         => ['string'],
            'password_ssh'         => ['string'],
            'port_ssh'             => ['string']
        ];
    }


    public function withValidator($validator)
    {
        if ($validator->errors()->any())
            return;

        $moduleSchedule = ModuleSchedule::find($this->route('schedule'));

        if (!$moduleSchedule)
        {
            throw new HttpResponseException(response()->json(['msg' => 'Selected Schedule does not exist!'], 404));
        }
        $validator->after(/**
         * @throws ValidationException
         */ function ($validator) use ($moduleSchedule) {

            if ($moduleSchedule->status == ModuleSchedule::RUNNING || $moduleSchedule->status == ModuleSchedule::FAILED)
                return $validator->errors()->add('module_id', 'The module has already been executed. You can no longer change its settings.');


            if ($this->input('username_ssh') || $this->input('password_ssh') || $this->input('port_ssh')) {

//                if (env('SSH_STATUS')) {
                    $sshHelper = new sshHelper(
                        $moduleSchedule->server->ip,
                        $this->input('username_ssh') ?? $moduleSchedule->username_ssh,
                        $this->input('password_ssh') ?? $moduleSchedule->password_ssh,
                        $this->input('port_ssh') ?? 22,
                        7
                    );

                    $sshHelper->testConnection();
//                }
            }
        });

        $this->merge(['moduleSchedule' => $moduleSchedule]);
    }



    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
