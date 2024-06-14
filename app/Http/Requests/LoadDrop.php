<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\BaseRequest;

class LoadDrop extends BaseRequest
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
            "powerStationId" => "required|exists:power_stations,identifier",
            "load" => "required|numeric",
            "previousLoad" => "required|numeric",
            "referenceLoad" => "required|numeric",
            "timeOfDrop" => "required|date",
            "calType" => "string",
            "acknowledgedAt" => "nullable|date"
        ];
    }
}
