<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\DonIntelligenceService;
use Illuminate\Http\Request;

class DonSmartMatchController extends Controller
{
    public function __invoke(Request $request, DonIntelligenceService $service)
    {
        $request->validate([
            'description' => 'required|string|min:3',
            'quantity'    => 'required|integer|min:1',
        ]);

        $recommendations = $service->recommendAssociations(
            $request->input('description'),
            (int) $request->input('quantity')
        );

        return response()->json($recommendations);
    }
}