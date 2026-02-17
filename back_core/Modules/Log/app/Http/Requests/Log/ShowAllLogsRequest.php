<?php

namespace Modules\Log\Http\Requests\Log;

use Illuminate\Foundation\Http\FormRequest;

class ShowAllLogsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'type-log' => ['string', 'in:server,app'],
            'search' => ['string', 'min:1', 'max:122']
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
