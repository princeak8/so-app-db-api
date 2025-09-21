<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\BaseRequest;

class PowerData extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'data' => 'required|array|min:1',
            'data.*.powerStationId' => 'required|string',
            'data.*.load' => 'required|numeric|min:0',
            'data.*.frequency' => 'nullable|numeric|min:0|max:100',
            'data.*.capturedAt' => 'required|date',
            'data.*.unitsData' => 'sometimes|array',
            'data.*.unitData.*.id' => 'required_with:data.*.unitData|string',
            'data.*.unitData.*.mw' => 'required_with:data.*.unitData|numeric|min:0',
            'data.*.unitData.*.kv' => 'required_with:data.*.unitData|numeric|min:0',
            'data.*.unitData.*.a' => 'required_with:data.*.unitData|numeric|min:0',
            'data.*.unitData.*.mx' => 'required_with:data.*.unitData|numeric|min:0',
            'data.*.unitData.*.frequency' => 'required_with:data.*.unitData|numeric|min:0|max:100'
        ];
    }
}
