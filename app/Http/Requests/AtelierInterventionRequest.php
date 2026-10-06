<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AtelierInterventionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // propriété de la demande vérifiée dans le contrôleur
    }

    public function rules(): array
    {
        return [
            'titre' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:1000'],
            'cout_estime' => ['nullable', 'numeric', 'min:0', 'max:99999'],
            'date_debut' => ['nullable', 'date'],
            'date_fin' => ['nullable', 'date', 'after_or_equal:date_debut'],
        ];
    }

    public function attributes(): array
    {
        return ['cout_estime' => 'coût estimé', 'date_debut' => 'date de début', 'date_fin' => 'date de fin'];
    }
}
