<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\ChatbotVetementService;
use Illuminate\Http\Request;
use RuntimeException;

class VetementChatController extends Controller
{
    public function message(Request $request, ChatbotVetementService $chat)
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:500'],
            'historique' => ['nullable', 'array', 'max:10'],
            'historique.*.role' => ['required', 'in:user,assistant'],
            'historique.*.text' => ['required', 'string', 'max:2000'],
        ]);

        try {
            return response()->json($chat->repondre($data['message'], $data['historique'] ?? []));
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 503);
        }
    }
}