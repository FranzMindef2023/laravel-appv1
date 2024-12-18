<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class StorePuestosRequest extends FormRequest
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
       // Asegúrate de obtener el ID correctamente
       $id = $this->route('puesto') ?? null;

        return [
            'nompuesto' => [
                'required',
                'string',
                'max:100',
                Rule::unique('puestos', 'nompuesto')->ignore($id, 'idpuesto')
            ],
            'status' => 'required|boolean',
 
        ];
    }
    public function messages()
    {
        return [
            'nompuesto.required' => 'El nombre del puesto es obligatorio.',
            'nompuesto.string' => 'El nombre del puesto debe ser un texto.',
            'nompuesto.max' => 'El nombre del puesto no debe superar los 100 caracteres.',
            'nompuesto.unique' => 'El nombre del puesto ya existe.',
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
