<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssociationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // L'accès est déjà restreint par le middleware role:admin.
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('associations')->ignore($this->route('association')?->id)],
            // Compte « association » qui traitera les dons ; un compte ne gère qu'une association.
            'manager_id' => [
                'nullable',
                Rule::exists('users', 'id')->where('role', UserRole::Association->value),
                Rule::unique('associations', 'manager_id')->ignore($this->route('association')?->id),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+\s().-]{6,30}$/'],
            'city' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom de l’association est obligatoire.',
            'name.unique' => 'Une association porte déjà ce nom.',
            'email.email' => 'Saisissez une adresse e-mail valide.',
            'manager_id.exists' => 'Ce compte doit avoir le rôle « Association ».',
            'manager_id.unique' => 'Ce compte gère déjà une autre association.',
            'phone.regex' => 'Le numéro de téléphone n’est pas valide.',
        ];
    }
}
