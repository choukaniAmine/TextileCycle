<?php

namespace App\Http\Controllers\Front;

use App\Enums\EtatVetement;
use App\Enums\TypeVetement;
use App\Exceptions\AiException;
use App\Http\Controllers\Controller;
use App\Models\Vetement;
use App\Services\VetementAiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VetementAiController extends Controller
{
    public function __construct(private VetementAiService $ai)
    {
    }

    public function analyser(Request $request): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        try {
            return response()->json($this->ai->analyserPhoto($request->file('image')));
        } catch (AiException $e) {
            return response()->json(['message' => $e->getMessage()], 503);
        }
    }

    public function description(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'categorie_id' => ['nullable', 'exists:categories,id'],
            'etat' => ['nullable', Rule::enum(EtatVetement::class)],
            'taille' => ['nullable', 'string', 'max:10'],
        ]);

        $data += ['description' => '', 'categorie_id' => null, 'etat' => '', 'taille' => ''];

        try {
            return response()->json(['description' => $this->ai->ameliorerDescription($data)]);
        } catch (AiException $e) {
            return response()->json(['message' => $e->getMessage()], 503);
        }
    }

    public function idees(Vetement $vetement): JsonResponse
    {
        $eligible = $vetement->type === TypeVetement::Transformation
            || $vetement->etat === EtatVetement::Use;

        if (! $eligible) {
            return response()->json(['message' => "Les idées de seconde vie sont réservées aux vêtements à transformer ou usés."], 422);
        }

        try {
            return response()->json(['idees' => $this->ai->idees($vetement)]);
        } catch (AiException $e) {
            return response()->json(['message' => $e->getMessage()], 503);
        }
    }
}