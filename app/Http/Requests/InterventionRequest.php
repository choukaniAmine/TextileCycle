<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InterventionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titre' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:1000'],
            'statut' => ['required', Rule::in(['en_attente', 'en_cours', 'terminee'])],
            'atelier_id' => ['nullable', Rule::exists('users', 'id')->where('role', 'atelier')],
            'cout_estime' => ['nullable', 'numeric', 'min:0', 'max:99999'],
            'date_debut' => ['nullable', 'date'],
            'date_fin' => ['nullable', 'date', 'after_or_equal:date_debut'],
        ];
    }

    public function attributes(): array
    {
        return ['atelier_id' => 'atelier', 'cout_estime' => 'coût estimé', 'date_debut' => 'date de début', 'date_fin' => 'date de fin'];
    }
}
