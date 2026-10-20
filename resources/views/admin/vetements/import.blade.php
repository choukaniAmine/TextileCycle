@extends('layouts.admin')
@section('title', 'Import CSV de vêtements')
@section('content')

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('import_erreurs'))
    <div class="alert alert-warning">
        <strong>Lignes ignorées :</strong>
        <ul class="mb-0">
            @foreach(session('import_erreurs') as $e) <li>{{ $e }}</li> @endforeach
        </ul>
    </div>
@endif

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Importer un fichier CSV</h3></div>
            <form method="POST" action="{{ route('admin.vetements.import.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label for="fichier">Fichier (.csv, 500 lignes max)</label>
                        <input type="file" name="fichier" id="fichier" accept=".csv,.txt"
                               class="form-control-file @error('fichier') is-invalid @enderror" required>
                        @error('fichier') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-primary"><i class="fas fa-upload"></i> Importer</button>
                    <a href="{{ route('admin.vetements.import.modele') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-download"></i> Télécharger le modèle
                    </a>
                    <a href="{{ route('admin.vetements.index') }}" class="btn btn-light">Retour</a>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Format attendu</h3></div>
            <div class="card-body">
                <p>Colonnes : <code>nom ; description ; taille ; etat ; type ; categorie</code> (la description est facultative).</p>
                <p class="mb-1"><strong>États acceptés :</strong> {{ collect($etats)->map->label()->implode(', ') }}</p>
                <p class="mb-1"><strong>Types acceptés :</strong> {{ collect($types)->map->label()->implode(', ') }}</p>
                <p class="mb-0"><strong>Catégories existantes :</strong> {{ $categories->pluck('nom')->implode(', ') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection