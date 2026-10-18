<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\AiDamageAnalysisService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiDiagnosisController extends Controller
{
    public function __construct(
        protected AiDamageAnalysisService $aiService
    ) {}

    /**
     * Analyse l'image du vêtement endommagé via l'IA.
     */
    public function analyze(Request $request): JsonResponse
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240', // max 10 Mo
        ], [
            'photo.required' => 'Veuillez sélectionner ou prendre une photo de votre vêtement.',
            'photo.image' => 'Le fichier fourni doit être une image valide.',
            'photo.mimes' => 'Formats acceptés : JPEG, PNG, JPG, WEBP.',
            'photo.max' => 'L\'image ne doit pas dépasser 10 Mo.',
        ]);

        try {
            $result = $this->aiService->analyze($request->file('photo'));

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible d\'analyser l\'image pour le moment : ' . $e->getMessage(),
            ], 500);
        }
    }
}
