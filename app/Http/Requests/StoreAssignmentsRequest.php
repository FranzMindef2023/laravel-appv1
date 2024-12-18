<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreAssignmentsRequest extends FormRequest
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
        // Obtener el ID del recurso actual desde la ruta
        $id = $this->route('assignment') ?? null;
        
        return [
            'gestion' => 'required|integer|min:2000|max:' . date('Y'),
            'idpersona' => 'required|exists:personas,idpersona',
            'idorg' => 'required|exists:organizacion,idorg',
            'idpuesto' => 'required|exists:puestos,idpuesto',
            'startdate' => 'required|date',
            'enddate' => 'nullable|date|after_or_equal:startdate',
            'status' => 'required|boolean',
            'motivo' => 'nullable|string|max:255', // Agregado para el campo motivo
        ];
    }
    public function messages()
    {
        return [
            'gestion.required' => 'El año de gestión es obligatorio.',
            'gestion.integer' => 'El año de gestión debe ser un número válido.',
            'gestion.min' => 'El año de gestión no puede ser anterior a 2000.',
            'gestion.max' => 'El año de gestión no puede ser en el futuro.',

            'idpersona.required' => 'El ID de la persona es obligatorio.',
            'idpersona.exists' => 'La persona seleccionada no existe.',

            'idorg.required' => 'El ID de la organización es obligatorio.',
            'idorg.exists' => 'La organización seleccionada no existe.',

            'idpuesto.required' => 'El ID del puesto es obligatorio.',
            'idpuesto.exists' => 'El puesto seleccionado no existe.',

            'startdate.required' => 'La fecha de inicio es obligatoria.',
            'startdate.date' => 'La fecha de inicio debe ser una fecha válida.',
            'startdate.before_or_equal' => 'La fecha de inicio debe ser anterior o igual a la fecha de fin.',

            'enddate.date' => 'La fecha de fin debe ser una fecha válida.',
            'enddate.after_or_equal' => 'La fecha de fin debe ser posterior o igual a la fecha de inicio.',

            'status.boolean' => 'El campo de estado debe ser verdadero o falso.',

            // Mensajes para el campo motivo
            'motivo.string' => 'El motivo debe ser un texto válido.',
            'motivo.max' => 'El motivo no puede tener más de 255 caracteres.',
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
