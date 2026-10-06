<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'atelier_id' => ['required', 'exists:ateliers,id'],
            'nom' => ['required', 'string', 'min:3', 'max:150'],
            'type_service' => ['required', 'string', 'in:Reparation,Retouche,Transformation,Customisation,Upcycling,Autre'],
            'description' => ['nullable', 'string', 'max:1500'],
            'tarif_estime' => ['required', 'numeric', 'min:0', 'max:9999.99'],
            'duree_estimee' => ['nullable', 'string', 'max:50'],
            'disponible' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'atelier_id.required' => 'Veuillez sélectionner un atelier.',
            'atelier_id.exists' => 'L\'atelier sélectionné est invalide.',
            'nom.required' => 'Le nom du service est obligatoire.',
            'nom.min' => 'Le nom du service doit contenir au moins :min caractères.',
            'type_service.required' => 'Le type de service est obligatoire.',
            'type_service.in' => 'Le type de service choisi n\'est pas valide.',
            'tarif_estime.required' => 'Le tarif estimé est obligatoire.',
            'tarif_estime.numeric' => 'Le tarif doit être une valeur numérique.',
            'tarif_estime.min' => 'Le tarif ne peut pas être négatif.',
        ];
    }

    public function attributes(): array
    {
        return [
            'atelier_id' => 'atelier',
            'nom' => 'nom du service',
            'type_service' => 'type de service',
            'description' => 'description',
            'tarif_estime' => 'tarif estimé',
            'duree_estimee' => 'durée estimée',
            'disponible' => 'disponibilité',
        ];
    }
}
