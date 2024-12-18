<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class StoreTipoNovedadesRequest extends FormRequest
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
        $id = $this->route('tiponovedade') ?? null;
        return [
            'novedad' => ['required',
                        'string',
                        'max:100',
                        Rule::unique('tiponovedad', 'novedad')->ignore($id, 'idnov')
                        ],
                        'status' => 'required|boolean',
        ];
    }
    public function messages()
    {
        return [
            'novedad.required' => 'El nombre de la novedad es obligatorio.',
            'novedad.string' => 'El nombre de la novedad debe ser un texto.',
            'novedad.max' => 'El nombre de la novedad no debe superar los 100 caracteres.',
            'novedad.unique' => 'El nombre de la novedad ya existe.',
            'status.boolean' => 'El campo de estado debe ser verdadero o falso.',
        ];
    }
     protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'message' => 'Datos de entrada no válidos.',
            'errors' => $validator->errors(),
        ], 422));
    }
}
