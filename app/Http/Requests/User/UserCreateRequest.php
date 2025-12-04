<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserCreateRequest extends FormRequest
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
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'nombre' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'rut' => 'required|string|unique:users,rut',
            'direccion' => 'nullable|string|max:500',
            'telefono' => 'nullable|string|max:20',
            'celular' => 'nullable|string|max:20',
            'role_slug' => 'required|string|exists:roles,slug',
        ];
    }
}
