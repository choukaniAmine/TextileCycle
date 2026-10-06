<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Atelier;
use App\Models\Avis;
use Illuminate\Http\Request;

class AvisController extends Controller
{
    /**
     * Enregistrer un nouvel avis (utilisateur connecté).
     */
    public function store(Request $request, Atelier $atelier)
    {
        $request->validate([
            'note'        => 'required|integer|min:1|max:5',
            'commentaire' => 'nullable|string|max:500',
        ], [
            'note.required' => 'Veuillez sélectionner une note.',
            'note.min'      => 'La note doit être entre 1 et 5.',
            'note.max'      => 'La note doit être entre 1 et 5.',
            'commentaire.max' => 'Le commentaire ne peut pas dépasser 500 caractères.',
        ]);

        // Vérifier si l'utilisateur a déjà laissé un avis
        $existant = Avis::where('atelier_id', $atelier->id)
                        ->where('user_id', auth()->id())
                        ->first();

        if ($existant) {
            // Mise à jour de l'avis existant
            $existant->update([
                'note'        => $request->note,
                'commentaire' => $request->commentaire,
            ]);
            return back()->with('success', 'Votre avis a été mis à jour. Merci !');
        }

        // Création d'un nouvel avis
        Avis::create([
            'atelier_id'  => $atelier->id,
            'user_id'     => auth()->id(),
            'note'        => $request->note,
            'commentaire' => $request->commentaire,
        ]);

        return back()->with('success', 'Votre avis a été publié. Merci pour votre contribution !');
    }

    /**
     * Supprimer son propre avis.
     */
    public function destroy(Avis $avis)
    {
        abort_unless($avis->user_id === auth()->id(), 403);
        $atelier = $avis->atelier;
        $avis->delete();
        return redirect()->route('ateliers.show', $atelier)
                         ->with('success', 'Votre avis a été supprimé.');
    }
}
