<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Permitir la validación
    }

    public function rules()
    {
        // Asegúrate de obtener el ID correctamente
       $id = $this->route('usuario') ?? null;
        return [
            'ci' => ['required',
                    'string',
                    Rule::unique('users', 'ci')->ignore($id, 'iduser')],
            'grado' => 'required|string|min:3|max:30',
            'nombres' => 'required|string|min:3|max:50',
            'appaterno' => 'nullable|string|min:3|max:50',
            'apmaterno' => 'nullable|string|min:3|max:50',
            'email' => ['required',
                        'email',
                        Rule::unique('users', 'email')->ignore($id, 'iduser')],
            'celular' => 'nullable|digits_between:8,15',
            'usuario' =>['required',
                        'string',
                        'min:3',
                        Rule::unique('users', 'usuario')->ignore($id, 'iduser'),
                        'max:30'],
            'password' => 'nullable|string|min:8|max:250',
            'status' => 'required|boolean',
            'token' => 'nullable|string|max:191',
            'idorg' => 'required|integer|exists:organizacion,idorg',
            'idpuesto' => 'required|integer|exists:puestos,idpuesto',
        ];
    }

    public function messages()
    {
        return [
            'ci.required' => 'El CI es obligatorio.',
            'ci.unique' => 'Este CI ya está en uso.',
            'grado.required' => 'El grado es obligatorio.',
            'grado.min' => 'El grado debe tener al menos 3 caracteres.',

            'nombres.required' => 'El nombre es obligatorio.',
            'nombres.min' => 'El nombre debe tener al menos 3 caracteres.',
            'appaterno.min' => 'El apellido paterno debe tener al menos 3 caracteres.',
            'apmaterno.min' => 'El apellido materno debe tener al menos 3 caracteres.',
            'email.required' => 'El correo es obligatorio.',
            'email.email' => 'El correo debe ser un correo electrónico válido.',
            'email.unique' => 'Este correo ya está en uso.',
            'email.max' => 'El correo no debe exceder los 250 caracteres.',
            'celular.digits_between' => 'El número de celular debe tener entre 8 y 15 dígitos.',
            'usuario.required' => 'El nombre de usuario es obligatorio.',
            'usuario.min' => 'El nombre de usuario debe tener al menos 3 caracteres.',
            'usuario.unique' => 'Este nombre de usuario ya está en uso.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'status.required' => 'El estado es obligatorio.',
            'status.boolean' => 'El estado debe ser verdadero o falso.',
            'idorg.required' => 'El identificador de organización es obligatorio.',
            'idorg.integer' => 'El identificador de organización debe ser un número entero.',
            'idorg.exists' => 'La organización especificada no existe.',
            'idpuesto.required' => 'El identificador de puesto es obligatorio.',
            'idpuesto.integer' => 'El identificador de puesto debe ser un número entero.',
            'idpuesto.exists' => 'El puesto especificado no existe.',
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
