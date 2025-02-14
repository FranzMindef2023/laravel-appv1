<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class StoreNovedadesRequest extends FormRequest
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
            'idassig' => 'required|integer|exists:assignments,idassig',
            'idnov' => 'required|integer|exists:tiponovedad,idnov',
            'descripcion' => 'nullable|string|max:255',
            'startdate' => 'required|date',
            'enddate' => 'nullable|date|after_or_equal:startdate',
            'activo' => 'required|boolean',
            'iduserreg'=>'required|integer',
        ];
    }

    public function messages()
    {
        return [
            'idassig.required' => 'El ID de asignación es obligatorio.',
            'idassig.integer' => 'El ID de asignación debe ser un número entero.',
            'idassig.exists' => 'La asignación especificada no existe.',
            
            'idnov.required' => 'El ID de novedad es obligatorio.',
            'idnov.integer' => 'El ID de novedad debe ser un número entero.',
            'idnov.exists' => 'La novedad especificada no existe.',
            
            'descripcion.string' => 'La descripción debe ser una cadena de texto.',
            'descripcion.max' => 'La descripción no puede tener más de 255 caracteres.',
            
            'startdate.required' => 'La fecha de inicio es obligatoria.',
            'startdate.date' => 'La fecha de inicio debe ser una fecha válida.',
            
            'enddate.date' => 'La fecha de finalización debe ser una fecha válida.',
            'enddate.after_or_equal' => 'La fecha de finalización debe ser igual o posterior a la fecha de inicio.',
            
            'activo.required' => 'El estado activo es obligatorio.',
            'activo.boolean' => 'El estado activo debe ser verdadero o falso.',
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
