<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class StoreRolesRequest extends FormRequest
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
        $id = $this->route('role') ?? null;
        return [
            'rol' =>['required',
                    'string',
                    'min:3',
                    'max:30',
                    Rule::unique('roles', 'rol')->ignore($id, 'idrol')],
                    'status' => 'required|boolean',
        ];
    }
    public function messages()
    {
        return [
            'rol.required' => 'El rol de usuario es obligatorio.',
            'rol.min' => 'El rol debe tener al menos 3 caracteres.',
            'rol.unique' => 'Este rol de usuario ya existe',
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
