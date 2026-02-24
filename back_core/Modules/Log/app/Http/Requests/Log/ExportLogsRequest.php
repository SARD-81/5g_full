<?php

namespace Modules\Log\Http\Requests\Log;

use Illuminate\Foundation\Http\FormRequest;

class ExportLogsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'type-log' => ['string', 'in:server,app'],
            'search' => ['string', 'min:1', 'max:122'],
            'format' => ['required', 'string', 'in:csv,json'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:50000'],
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
