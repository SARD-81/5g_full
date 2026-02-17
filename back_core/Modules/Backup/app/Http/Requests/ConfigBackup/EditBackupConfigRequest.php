<?php

namespace Modules\Backup\Http\Requests\ConfigBackup;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Backup\Utilities\DirectoryUtilities;

class EditBackupConfigRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'id'               => ['required', 'integer', 'exists:backup_configs,id'],
            'destination_path' => ['string'],
            'run_backup_at'    => ['date_format:d H:i'],
        ];
    }


    public function withValidator ($validator) : void
    {
        if ($validator->errors()->any())
            return;

        $validator->after(function ($validator) {

            DirectoryUtilities::basicValidator($this->input('destination_path'));
            DirectoryUtilities::OwnerValidator($this->input('destination_path'));
            DirectoryUtilities::PermissionValidator($this->input('destination_path'));

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
