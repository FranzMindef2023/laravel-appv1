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
            'idorgani' => 'required|exists:organizacion,idorg',
            'code' => 'required|exists:reparticiones,code',
            'idhijastro' => 'required|exists:organizacion,idorg',
            'idpuesto' => 'required|exists:puestos,idpuesto',
            'startdate' => 'required|date',
            'enddate' => 'nullable|date|after_or_equal:startdate',
            'status' => 'required|boolean',
            'motivo' => 'nullable|string|max:255', // Agregado para el campo motivo 
            'motivofin' => 'nullable|string|max:255',
            'estado' => 'required|string|in:A,C,D',
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

            'idorgani.required' => 'El ID de la reparticion es obligatorio.',
            'idorgani.exists' => 'La reparticion seleccionada no existe.',

            'code.required' => 'El code del destino es obligatorio.',
            'code.exists' => 'El destino seleccionada no existe.',

            'idhijastro.required' => 'El ID de la unidad organizacional es obligatorio.',
            'idhijastro.exists' => 'La unidad organizacional seleccionada no existe.',

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
            'motivo.string' => 'El motivo de destino debe ser un texto válido.',
            'motivo.max' => 'El motivo de destino no puede tener más de 255 caracteres.',
            // Mensajes para el campo motivo
            'motivofin.string' => 'El motivo de repliegue debe ser un texto válido.',
            'motivofin.max' => 'El motivofin de repliegue no puede tener más de 255 caracteres.',
            'estado.required' =>'El capo de estado de cambio es requerido.',
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
