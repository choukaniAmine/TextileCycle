<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAtelierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom' => ['required', 'string', 'min:3', 'max:150', 'unique:ateliers,nom'],
            'description' => ['nullable', 'string', 'min:10', 'max:2000'],
            'adresse' => ['required', 'string', 'min:5', 'max:255'],
            'ville' => ['required', 'string', 'min:2', 'max:100'],
            'code_postal' => ['nullable', 'string', 'max:10'],
            'telephone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:2048'],
            'horaires' => ['nullable', 'string', 'max:255'],
            'est_actif' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom de l\'atelier est obligatoire.',
            'nom.min' => 'Le nom de l\'atelier doit comporter au moins :min caractères.',
            'nom.unique' => 'Un atelier avec ce nom existe déjà.',
            'adresse.required' => 'L\'adresse de l\'atelier est obligatoire.',
            'ville.required' => 'La ville est obligatoire.',
            'telephone.required' => 'Le numéro de téléphone est obligatoire.',
            'email.email' => 'Veuillez saisir une adresse email valide.',
            'image.image' => 'Le fichier doit être une image valide.',
            'image.mimes' => 'Formats d\'image acceptés : jpeg, png, jpg, webp, svg.',
            'image.max' => 'L\'image ne doit pas dépasser 2 Mo.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nom' => 'nom de l\'atelier',
            'description' => 'description',
            'adresse' => 'adresse',
            'ville' => 'ville',
            'code_postal' => 'code postal',
            'telephone' => 'téléphone',
            'email' => 'adresse email',
            'image' => 'photo de l\'atelier',
            'horaires' => 'horaires d\'ouverture',
            'est_actif' => 'statut actif',
        ];
    }
}
