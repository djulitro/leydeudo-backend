<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
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
            'email' => 'sometimes|required|email|unique:users,email,' . $this->route('id_user'),
            'nombre' => 'sometimes|required|string|max:255',
            'apellidos' => 'sometimes|required|string|max:255',
            'rut' => 'sometimes|required|string|max:20|unique:users,rut,' . $this->route('id_user'),
            'direccion' => 'sometimes|nullable|string|max:500',
            'telefono' => 'sometimes|nullable|string|max:20',
            'celular' => 'sometimes|nullable|string|max:20',
            'role_slug' => 'sometimes|required|string|exists:roles,slug',
        ];
    }
}
