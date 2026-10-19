<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EtatVetement;
use App\Enums\TypeVetement;
use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\Vetement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VetementController extends Controller
{
    public function index(Request $request)
    {
        $vetements = Vetement::with('categorie')
            ->when($request->filled('categorie_id'), fn ($q) => $q->where('categorie_id', $request->categorie_id))
            ->when($request->filled('etat'), fn ($q) => $q->where('etat', $request->etat))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.vetements.index', [
            'vetements' => $vetements,
            'categories' => Categorie::orderBy('nom')->get(),
            'etats' => EtatVetement::cases(),
        ]);
    }

    public function create()
    {
        return view('admin.vetements.create', $this->formData());
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('vetements', 'public');
        }
        $data['user_id'] = $request->user()->id;

        Vetement::create($data);
        return redirect()->route('admin.vetements.index')->with('success', 'Vêtement ajouté.');
    }

    public function edit(Vetement $vetement)
    {
        return view('admin.vetements.edit', ['vetement' => $vetement] + $this->formData());
    }

    public function update(Request $request, Vetement $vetement)
    {
        $data = $this->validated($request);
        if ($request->hasFile('image')) {
            if ($vetement->image) {
                Storage::disk('public')->delete($vetement->image);
            }
            $data['image'] = $request->file('image')->store('vetements', 'public');
        }

        $vetement->update($data);
        return redirect()->route('admin.vetements.index')->with('success', 'Vêtement modifié.');
    }

    public function destroy(Vetement $vetement)
    {
        if ($vetement->image) {
            Storage::disk('public')->delete($vetement->image);
        }
        $vetement->delete();
        return redirect()->route('admin.vetements.index')->with('success', 'Vêtement supprimé.');
    }

    private function formData(): array
    {
        return [
            'categories' => Categorie::orderBy('nom')->get(),
            'etats' => EtatVetement::cases(),
            'types' => TypeVetement::cases(),
        ];
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'taille' => ['required', 'string', 'max:10'],
            'etat' => ['required', Rule::enum(EtatVetement::class)],
            'type' => ['required', Rule::enum(TypeVetement::class)],
            'categorie_id' => ['required', 'exists:categories,id'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);
    }
    public function statistiques()
{
    // Libellé d'un enum (label() si elle existe, sinon la valeur brute)
    $libelle = fn ($enum) => method_exists($enum, 'label') ? $enum->label() : ($enum->value ?? $enum->name);

    // DB::table évite les casts d'enum pendant le regroupement
    $parEtatBrut = DB::table('vetements')->select('etat', DB::raw('COUNT(*) as total'))->groupBy('etat')->pluck('total', 'etat');
    $parTypeBrut = DB::table('vetements')->select('type', DB::raw('COUNT(*) as total'))->groupBy('type')->pluck('total', 'type');

    $parEtat = collect(EtatVetement::cases())->mapWithKeys(fn ($c) => [$libelle($c) => (int) ($parEtatBrut[$c->value] ?? 0)]);
    $parType = collect(TypeVetement::cases())->mapWithKeys(fn ($c) => [$libelle($c) => (int) ($parTypeBrut[$c->value] ?? 0)]);

    // Par catégorie + calcul de l'impact
    $categories = Categorie::withCount('vetements')->orderBy('nom')->get();
    $parCategorie = $categories->pluck('vetements_count', 'nom');

    $kg = 0;
    $eau = 0;
    foreach ($categories as $cat) {
        [$poids, $litres] = $this->coefficients($cat->nom);
        $kg += $poids * $cat->vetements_count;
        $eau += $litres * $cat->vetements_count;
    }

    // Évolution sur les 12 derniers mois
    $debut = now()->subMonths(11)->startOfMonth();
    $ajouts = Vetement::where('created_at', '>=', $debut)->pluck('created_at')
        ->countBy(fn ($d) => $d->format('Y-m'));

    $parMois = collect(range(0, 11))->mapWithKeys(function ($i) use ($debut, $ajouts) {
        $m = $debut->copy()->addMonths($i);
        return [$m->translatedFormat('M Y') => (int) ($ajouts[$m->format('Y-m')] ?? 0)];
    });

    return view('admin.vetements.statistiques', [
        'total' => Vetement::count(),
        'kg' => round($kg, 1),
        'eau' => (int) round($eau),
        'joursEau' => round($eau / 150),   // ~150 L / jour / personne
        'parCategorie' => $parCategorie,
        'parEtat' => $parEtat,
        'parType' => $parType,
        'parMois' => $parMois,
    ]);
}

/** [poids moyen en kg, litres d'eau pour fabriquer] selon le nom de la catégorie. */
private function coefficients(string $nomCategorie): array
{
    $table = [
        'jean'      => [0.6, 7500],
        'pantalon'  => [0.5, 5000],
        'tee'       => [0.2, 2700],   // t-shirt, tee-shirt
        'chemise'   => [0.25, 3000],
        'robe'      => [0.4, 4000],
        'jupe'      => [0.3, 3000],
        'pull'      => [0.5, 4500],
        'veste'     => [0.9, 6000],
        'manteau'   => [1.2, 8000],
        'chaussure' => [0.8, 8000],
        'short'     => [0.25, 3000],
    ];

    $nom = Str::lower(Str::ascii($nomCategorie));
    foreach ($table as $motCle => $valeurs) {
        if (str_contains($nom, $motCle)) {
            return $valeurs;
        }
    }

    return [0.4, 3000]; // valeur par défaut
}
}