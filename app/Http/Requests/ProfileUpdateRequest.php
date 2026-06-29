<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'bio' => ['nullable', 'string', 'max:700'],
            'city' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'in:colombia,ecuador'],
            'birth_date' => ['nullable', 'date', 'before:-15 years'],
            'gender' => ['nullable', 'in:male,female,non_binary,other'],
            'pronouns' => ['nullable', 'string', 'max:50'],
            'gender_preference' => ['nullable', 'array'],
            'gender_preference.*' => ['in:male,female,non_binary,other'],
        ];
    }

    /**
     * Get custom attribute names for validation errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'nombre',
            'email' => 'correo electrónico',
            'bio' => 'biografía',
            'city' => 'ciudad',
            'birth_date' => 'fecha de nacimiento',
            'gender' => 'género',
            'pronouns' => 'pronombres',
            'gender_preference' => 'busca conectar con',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'birth_date.before' => 'Debes tener al menos 15 años.',
        ];
    }
}
