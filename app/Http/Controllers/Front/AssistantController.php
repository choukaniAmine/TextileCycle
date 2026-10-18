<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\Grok\GrokException;
use App\Services\Grok\RepairAssistant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

/** Analyse seulement : ne crée JAMAIS de demande. L'utilisateur relit et envoie le formulaire lui-même. */
class AssistantController extends Controller
{
    public function __invoke(Request $request, RepairAssistant $assistant): JsonResponse
    {
        $data = $request->validate([
            'description' => ['required', 'string', 'min:15', 'max:1500'],
        ]);

        $key = 'grok-assistant:'.$request->user()->id;
        if (RateLimiter::tooManyAttempts($key, config('ai.grok.rate_limit_per_minute'))) {
            return response()->json([
                'ok' => false,
                'message' => 'Vous avez utilisé l’assistant plusieurs fois de suite. Réessayez dans '.RateLimiter::availableIn($key).' s.',
            ], 429);
        }
        RateLimiter::hit($key, 60);

        try {
            return response()->json(['ok' => true, 'analyse' => $assistant->analyse($data['description'])]);
        } catch (GrokException $e) {
            report($e);

            return response()->json(['ok' => false, 'message' => $e->userMessage()], $e->httpStatus());
        }
    }
}
