<?php

namespace App\Http\Requests;

use App\Enums\TypeDemande;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DemandeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // l'accès est contrôlé par le middleware de rôle et le contrôleur
    }

    public function rules(): array
    {
        return [
            'titre' => ['required', 'string', 'max:120'],
            'type' => ['required', Rule::enum(TypeDemande::class)],
            'vetement' => ['required', 'string', 'max:120'],
            'description' => ['required', 'string', 'min:10', 'max:2000'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'urgent' => ['nullable', 'boolean'],
            'date_souhaitee' => ['nullable', 'date', 'after_or_equal:today'],
            // IA : caractéristiques utilisées pour l'estimation du temps de travail
            'categorie_vetement' => ['nullable', Rule::in(array_keys(config('repair.categories')))],
            'type_intervention' => ['nullable', Rule::in(array_keys(config('repair.interventions')))],
            'niveau_difficulte' => ['nullable', 'integer', 'between:1,3'],
        ];
    }

    public function attributes(): array
    {
        return [
            'titre' => 'titre', 'type' => 'type de demande', 'vetement' => 'vêtement',
            'description' => 'description', 'photo' => 'photo', 'date_souhaitee' => 'date souhaitée',
            'categorie_vetement' => 'type de vêtement', 'type_intervention' => 'type d’intervention',
            'niveau_difficulte' => 'ampleur du travail',
        ];
    }
}
