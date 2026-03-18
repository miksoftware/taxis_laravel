<?php

namespace App\Http\Requests\Usuario;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->esAdmin();
    }

    public function rules(): array
    {
        $usuarioId = $this->route('usuario')->id;

        return [
            'nombre' => ['required', 'string', 'min:2', 'max:50'],
            'apellidos' => ['required', 'string', 'min:2', 'max:100'],
            'email' => ['required', 'email', 'max:100', Rule::unique('usuarios')->ignore($usuarioId)],
            'username' => ['required', 'string', 'min:4', 'max:50', Rule::unique('usuarios')->ignore($usuarioId)],
            'telefono' => ['nullable', 'string', 'max:15'],
            'rol' => ['required', 'in:superadmin,administrador,operador'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.min' => 'El nombre debe tener al menos 2 caracteres.',
            'apellidos.required' => 'Los apellidos son obligatorios.',
            'email.required' => 'El email es obligatorio.',
            'email.unique' => 'Este email ya está registrado por otro usuario.',
            'username.required' => 'El nombre de usuario es obligatorio.',
            'username.unique' => 'Este nombre de usuario ya está en uso.',
            'rol.required' => 'Debe seleccionar un rol.',
        ];
    }
}
