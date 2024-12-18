<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class StorePersonasRequest extends FormRequest
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
        $id = $this->route('persona') ?? null;
        return [
            'nombres' => 'required|string|max:100',
            'appaterno' => 'nullable|string|max:50',
            'apmaterno' => 'nullable|string|max:50',
            'ci' =>['required','string','max:20',
                Rule::unique('personas', 'ci')->ignore($id, 'idpersona')  
            ],
            'complemento' => 'nullable|string|max:10',

            'codper' => ['required','string','max:20',
                Rule::unique('personas', 'codper')->ignore($id, 'idpersona')   
            ],
            'email' =>[
                'nullable','email','max:100',
                Rule::unique('personas', 'email')->ignore($id, 'idpersona')   
            ],
            'celular' => 'nullable|string|max:15',
            'fechnacimeinto' => 'required|date',
            'fechaegreso'=> 'required|date',
            'gsanguineo' => 'nullable|string|max:3',
            'carnetmil' =>[
                'nullable','string','max:20',
                Rule::unique('personas', 'carnetmil')->ignore($id, 'idpersona') 
            ],
            'carnetseg' =>['nullable','string','max:20',
            Rule::unique('personas', 'carnetseg')->ignore($id, 'idpersona') 
            ] ,
            'tipoper' => 'required|in:C,M',
            'estserv' => 'nullable|string|max:50',
            'idfuerza' => 'required|exists:fuerzas,idfuerza',
            'idespecialidad' => 'required|exists:especialidades,idespecialidad',
            'idgrado' => 'required|exists:grados,idgrado',
            'idsexo' => 'required|exists:sexos,idsexo',
            'idarma' => 'required|exists:armas,idarma',
            'idcv' => 'required|exists:statuscvs,idcv', 
            'status' => 'required|boolean',
            'idsituacion'=>'required|exists:situaciones,idsituacion',
            'idexpedicion'=>'required|exists:expediciones,idexpedicion',  
        ];
    }
    public function messages()
    {
        return [
            'nombres.required' => 'El nombre es obligatorio.',
            'nombres.string' => 'El nombre debe ser una cadena de texto.',
            'nombres.max' => 'El nombre no debe superar los 100 caracteres.',

            'appaterno.string' => 'El apellido paterno debe ser una cadena de texto.',
            'appaterno.max' => 'El apellido paterno no debe superar los 50 caracteres.',

            'apmaterno.string' => 'El apellido materno debe ser una cadena de texto.',
            'apmaterno.max' => 'El apellido materno no debe superar los 50 caracteres.',

            'ci.required' => 'La cédula de identidad es obligatoria.',
            'ci.string' => 'La cédula de identidad debe ser una cadena de texto.',
            'ci.max' => 'La cédula de identidad no debe superar los 20 caracteres.',
            'ci.unique' => 'La cédula de identidad ya está registrada.',

            'complemento.string' => 'El complemento debe ser una cadena de texto.',
            'complemento.max' => 'El complemento no debe superar los 10 caracteres.',

            'codper.required' => 'El código de persona es obligatorio.',
            'codper.string' => 'El código de persona debe ser una cadena de texto.',
            'codper.max' => 'El código de persona no debe superar los 20 caracteres.',
            'codper.unique' => 'El código de persona ya está registrado.',

            'email.email' => 'El correo electrónico debe ser válido.',
            'email.max' => 'El correo electrónico no debe superar los 100 caracteres.',
            'email.unique' => 'El correo electrónico ya está registrado.',

            'celular.string' => 'El número de celular debe ser una cadena de texto.',
            'celular.max' => 'El número de celular no debe superar los 15 caracteres.',

            'fechnacimeinto.required' => 'La fecha de nacimiento es obligatoria.',
            'fechnacimeinto.date' => 'La fecha de nacimiento debe ser una fecha válida.',

            'fechaegreso.required' => 'La fecha de egreso es obligatoria.',
            'fechaegreso.date' => 'La fecha de egreso debe ser una fecha válida.',

            'gsanguineo.string' => 'El grupo sanguíneo debe ser una cadena de texto.',
            'gsanguineo.max' => 'El grupo sanguíneo no debe superar los 3 caracteres.',

            'carnetmil.string' => 'El carnet militar debe ser una cadena de texto.',
            'carnetmil.max' => 'El carnet militar no debe superar los 20 caracteres.',
            'carnetmil.unique' => 'El carnet militar ya está registrado.',

            'carnetseg.string' => 'El carnet de seguro debe ser una cadena de texto.',
            'carnetseg.max' => 'El carnet de seguro no debe superar los 20 caracteres.',
            'carnetseg.unique' => 'El carnet de seguro ya está registrado.',

            'tipoper.required' => 'El tipo de persona es obligatorio.',
            'tipoper.in' => 'El tipo de persona debe ser "C" (Civil) o "M" (Militar).',

            'estserv.string' => 'El estado del servicio debe ser una cadena de texto.',
            'estserv.max' => 'El estado del servicio no debe superar los 50 caracteres.',

            'idfuerza.required' => 'El ID de la fuerza es obligatorio.',
            'idfuerza.exists' => 'La fuerza seleccionada no existe.',

            'idespecialidad.required' => 'El ID de la especialidad es obligatorio.',
            'idespecialidad.exists' => 'La especialidad seleccionada no existe.',

            'idgrado.required' => 'El ID del grado es obligatorio.',
            'idgrado.exists' => 'El grado seleccionado no existe.',

            'idsexo.required' => 'El ID del sexo es obligatorio.',
            'idsexo.exists' => 'El sexo seleccionado no existe.',

            'idarma.required' => 'El ID del arma es obligatorio.',
            'idarma.exists' => 'El arma seleccionada no existe.',

            'idcv.required' => 'El ID de estado civil es obligatorio.',
            'idcv.exists' => 'El estado civil seleccionado no existe.',

            'idsituacion.required' => 'La situación es obligatoria.',
            'idsituacion.exists' => 'La situación seleccionada no existe.',

            'idexpedicion.required' => 'El ID de expedición es obligatorio.',
            'idexpedicion.exists' => 'La expedición seleccionada no existe.',

            'status.required' => 'El estado es obligatorio.',
            'status.boolean' => 'El estado debe ser verdadero o falso.',
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
