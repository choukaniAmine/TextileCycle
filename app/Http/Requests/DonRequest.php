<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Validation commune aux dons (back office et front office). */
class DonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'association_id' => ['required', Rule::exists('associations', 'id')->where('is_active', true)],
            'description' => ['required', 'string', 'min:3', 'max:1000'],
            'quantity' => ['required', 'integer', 'min:1', 'max:1000'],
            'donated_at' => ['required', 'date', 'before_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];

        // Le back office peut désigner le donateur. Le statut n'est jamais saisi ici :
        // il est géré uniquement par l'association qui reçoit le don.
        if ($this->routeIs('admin.*')) {
            $rules['user_id'] = ['nullable', 'exists:users,id'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'association_id.required' => 'Choisissez l’association bénéficiaire.',
            'association_id.exists' => 'Cette association n’est pas disponible.',
            'description.required' => 'Décrivez les vêtements donnés (ex. 3 pantalons, 2 vestes).',
            'quantity.min' => 'La quantité doit être d’au moins 1 pièce.',
            'donated_at.before_or_equal' => 'La date du don ne peut pas être dans le futur.',
        ];
    }
}
