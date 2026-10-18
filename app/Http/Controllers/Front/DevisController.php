<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Atelier;
use App\Models\Devis;
use App\Models\DevisItem;
use App\Models\Service;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DevisController extends Controller
{
    /**
     * Enregistrer une simulation de devis et préparer le PDF.
     */
    public function store(Request $request, Atelier $atelier)
    {
        $validated = $request->validate([
            'nom_client'       => 'required|string|max:100',
            'email_client'     => 'required|email|max:150',
            'telephone_client' => 'nullable|string|max:30',
            'remarques'        => 'nullable|string|max:500',
            'services'         => 'required|array|min:1',
            'services.*'       => 'nullable|integer|min:0|max:20',
        ], [
            'services.required' => 'Veuillez sélectionner au moins un service.',
            'nom_client.required' => 'Votre nom complet est obligatoire.',
            'email_client.required' => 'Votre adresse e-mail est obligatoire.',
            'email_client.email' => 'Adresse e-mail invalide.',
        ]);

        // Filtrer les services sélectionnés avec une quantité > 0
        $selectedQuantities = array_filter($validated['services'], fn($qty) => (int)$qty > 0);

        if (empty($selectedQuantities)) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Veuillez sélectionner au moins une prestation avec une quantité supérieure à 0.',
                ], 422);
            }
            return back()->with('error', 'Veuillez sélectionner au moins une prestation.')->withInput();
        }

        // Récupérer les services réels appartenant à l'atelier
        $services = Service::where('atelier_id', $atelier->id)
            ->whereIn('id', array_keys($selectedQuantities))
            ->get();

        if ($services->isEmpty()) {
            return back()->with('error', 'Les services sélectionnés ne sont pas valides pour cet atelier.');
        }

        $montantTotal = 0;
        $totalPieces  = 0;
        $itemsData    = [];

        foreach ($services as $service) {
            $qty = (int)$selectedQuantities[$service->id];
            $prixUnitaire = (float)$service->tarif_estime;
            $sousTotal = round($prixUnitaire * $qty, 2);

            $montantTotal += $sousTotal;
            $totalPieces  += $qty;

            $itemsData[] = [
                'service_id'   => $service->id,
                'nom_service'  => $service->nom,
                'type_service' => $service->type_service,
                'prix_unitaire'=> $prixUnitaire,
                'quantite'     => $qty,
                'sous_total'   => $sousTotal,
            ];
        }

        // Impact écologique estimé (par vêtement réparé vs confection neuve)
        // ~6.5 kg CO2 et ~2500 L d'eau économisés par pièce
        $co2Economise   = round($totalPieces * 6.5, 2);
        $eauEconomisee  = $totalPieces * 2500;
        $dureeEstimee   = max(2, min(14, 2 + intdiv($totalPieces, 2)));

        $devis = DB::transaction(function () use ($atelier, $validated, $montantTotal, $co2Economise, $eauEconomisee, $dureeEstimee, $itemsData) {
            $devis = Devis::create([
                'atelier_id'          => $atelier->id,
                'user_id'             => auth()->id(),
                'nom_client'          => $validated['nom_client'],
                'email_client'        => $validated['email_client'],
                'telephone_client'    => $validated['telephone_client'] ?? null,
                'remarques'           => $validated['remarques'] ?? null,
                'montant_total'       => $montantTotal,
                'co2_economise'       => $co2Economise,
                'eau_economisee'      => $eauEconomisee,
                'duree_estimee_jours' => $dureeEstimee,
                'statut'              => 'valide',
            ]);

            foreach ($itemsData as $item) {
                $devis->items()->create($item);
            }

            return $devis;
        });

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'      => true,
                'message'      => 'Devis généré avec succès !',
                'reference'    => $devis->reference,
                'download_url' => route('devis.download', $devis),
                'stream_url'   => route('devis.stream', $devis),
                'total'        => number_format($devis->montant_total, 2, ',', ' ') . ' TND',
                'co2'          => $devis->co2_economise . ' kg',
                'eau'          => number_format($devis->eau_economisee, 0, ',', ' ') . ' L',
                'delai'        => $devis->duree_estimee_jours . ' jours',
            ]);
        }

        return redirect()->route('devis.download', $devis)
            ->with('success', "Votre devis {$devis->reference} a été généré avec succès.");
    }

    /**
     * Télécharger le devis sous format PDF.
     */
    public function download(Devis $devis)
    {
        $devis->load(['atelier', 'items.service']);
        $pdf = Pdf::loadView('pdf.devis', compact('devis'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("Devis_{$devis->reference}_{$devis->atelier->nom}.pdf");
    }

    /**
     * Prévisualiser / afficher le PDF dans le navigateur.
     */
    public function stream(Devis $devis)
    {
        $devis->load(['atelier', 'items.service']);
        $pdf = Pdf::loadView('pdf.devis', compact('devis'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream("Devis_{$devis->reference}.pdf");
    }
}
