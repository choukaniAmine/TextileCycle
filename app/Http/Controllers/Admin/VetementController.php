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
use App\Exports\VetementsExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class VetementController extends Controller
{
    public function index(Request $request)
{
    return view('admin.vetements.index', [
        'vetements' => $this->filtre($request)->paginate(10)->withQueryString(),
        'categories' => Categorie::orderBy('nom')->get(),
        'etats' => EtatVetement::cases(),
    ]);
}

/** Requête filtrée, partagée par la liste, les exports et les étiquettes. */
private function filtre(Request $request)
{
    return Vetement::with('categorie')
        ->when($request->filled('categorie_id'), fn ($q) => $q->where('categorie_id', $request->categorie_id))
        ->when($request->filled('etat'), fn ($q) => $q->where('etat', $request->etat))
        ->latest();
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
// ---------- EXPORTS ----------

public function exportPdf(Request $request)
{
    $vetements = $this->filtre($request)->get();

    return Pdf::loadView('admin.vetements.pdf', ['vetements' => $vetements])
        ->setPaper('a4', 'landscape')
        ->download('vetements-'.now()->format('Ymd-His').'.pdf');
}

public function exportExcel(Request $request)
{
    return Excel::download(
        new VetementsExport($this->filtre($request)),
        'vetements-'.now()->format('Ymd-His').'.xlsx'
    );
}

// ---------- ÉTIQUETTES QR ----------

/** Planche d'étiquettes pour la liste filtrée. */
public function etiquettes(Request $request)
{
    return view('admin.vetements.etiquettes', [
        'vetements' => $this->filtre($request)->limit(100)->get(),
    ]);
}

/** Étiquette d'un seul vêtement. */
public function etiquette(Vetement $vetement)
{
    $vetement->load('categorie');

    return view('admin.vetements.etiquettes', ['vetements' => collect([$vetement])]);
}

// ---------- IMPORT CSV ----------

public function importForm()
{
    return view('admin.vetements.import', [
        'etats' => EtatVetement::cases(),
        'types' => TypeVetement::cases(),
        'categories' => Categorie::orderBy('nom')->get(),
    ]);
}

public function importModele()
{
    $etat = EtatVetement::cases()[0]->label();
    $type = TypeVetement::cases()[0]->label();
    $categorie = Categorie::orderBy('nom')->value('nom') ?? 'Pantalon';

    return response()->streamDownload(function () use ($etat, $type, $categorie) {
        $f = fopen('php://output', 'w');
        fwrite($f, "\xEF\xBB\xBF"); // BOM UTF-8 pour qu'Excel lise bien les accents
        fputcsv($f, ['nom', 'description', 'taille', 'etat', 'type', 'categorie'], ';');
        fputcsv($f, ['Jean bleu', 'Jean droit en bon état', 'M', $etat, $type, $categorie], ';');
        fclose($f);
    }, 'modele-import-vetements.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
}

public function importStore(Request $request)
{
    $request->validate(['fichier' => ['required', 'file', 'mimes:csv,txt', 'max:2048']]);

    $handle = fopen($request->file('fichier')->getRealPath(), 'r');

    // Détection du séparateur (Excel français = ; / autres = ,) et suppression du BOM
    $premiere = fgets($handle);
    $premiere = preg_replace('/^\xEF\xBB\xBF/', '', $premiere);
    $sep = substr_count($premiere, ';') >= substr_count($premiere, ',') ? ';' : ',';

    $norm = fn ($s) => Str::lower(Str::ascii(trim((string) $s)));
    $entetes = array_map($norm, str_getcsv($premiere, $sep));

    $requises = ['nom', 'taille', 'etat', 'type', 'categorie'];
    $manquantes = array_diff($requises, $entetes);
    if ($manquantes) {
        fclose($handle);
        return back()->withErrors(['fichier' => 'Colonnes manquantes : '.implode(', ', $manquantes).'.']);
    }

    $categories = Categorie::all()->keyBy(fn ($c) => $norm($c->nom));
    $importes = 0;
    $erreurs = [];
    $ligne = 1;

    while (($row = fgetcsv($handle, 0, $sep)) !== false) {
        $ligne++;
        if (count(array_filter($row, fn ($v) => trim((string) $v) !== '')) === 0) {
            continue; // ligne vide
        }
        if ($ligne > 501) {
            $erreurs[] = 'Import limité à 500 lignes : les suivantes sont ignorées.';
            break;
        }

        $row = array_pad($row, count($entetes), '');
        $d = array_combine($entetes, array_slice($row, 0, count($entetes)));

        $etat = $this->resoudreEnum(EtatVetement::class, $d['etat']);
        $type = $this->resoudreEnum(TypeVetement::class, $d['type']);
        $categorie = $categories[$norm($d['categorie'])] ?? null;

        $problemes = [];
        if (trim($d['nom']) === '') $problemes[] = 'nom vide';
        if (trim($d['taille']) === '' || mb_strlen(trim($d['taille'])) > 10) $problemes[] = 'taille invalide';
        if (! $etat) $problemes[] = "état « {$d['etat']} » inconnu";
        if (! $type) $problemes[] = "type « {$d['type']} » inconnu";
        if (! $categorie) $problemes[] = "catégorie « {$d['categorie']} » introuvable";

        if ($problemes) {
            $erreurs[] = "Ligne {$ligne} : ".implode(', ', $problemes).'.';
            continue;
        }

        Vetement::create([
            'nom' => trim($d['nom']),
            'description' => trim($d['description'] ?? '') ?: null,
            'taille' => trim($d['taille']),
            'etat' => $etat,
            'type' => $type,
            'categorie_id' => $categorie->id,
            'user_id' => $request->user()->id,
        ]);
        $importes++;
    }
    fclose($handle);

    return redirect()->route('admin.vetements.import')
        ->with('success', "{$importes} vêtement(s) importé(s).")
        ->with('import_erreurs', $erreurs);
}

/** Trouve un cas d'enum par sa valeur ou son libellé (insensible à la casse et aux accents). */
private function resoudreEnum(string $enumClass, ?string $valeur): ?\BackedEnum
{
    $n = fn ($s) => Str::lower(Str::ascii(trim((string) $s)));
    $cherche = $n($valeur);

    foreach ($enumClass::cases() as $cas) {
        if ($n($cas->value) === $cherche || $n($cas->label()) === $cherche) {
            return $cas;
        }
    }

    return null;
}
}