@extends('layouts.front')
@section('title', 'Faire un don')
@section('content')
<div class="container py-5" style="max-width:640px">
    <h2 class="mb-1">Faire un don de vêtements</h2>
    <p class="text-muted">Indiquez ce que vous donnez : l’association validera votre don.</p>

    {{-- BOÎTE DE RECOMMANDATION IA (Cachée par défaut) --}}
    <div id="ai-recommendation" class="alert alert-info shadow-sm d-none mb-4" style="border-left: 4px solid #0d6efd;">
        <h6 class="alert-heading font-weight-bold"><i class="fas fa-robot"></i> Recommandation de l'IA</h6>
        <div id="ai-results" class="mt-2"></div>
    </div>

    <form method="POST" action="{{ route('dons.store') }}" novalidate>
        @csrf
        <div class="mb-3">
            <label for="description" class="form-label font-weight-bold">Vêtements donnés *</label>
            <textarea id="description" name="description" rows="3" placeholder="Ex. 3 pantalons, 2 vestes pour bébé..." class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="quantity" class="form-label font-weight-bold">Nombre de pièces *</label>
                <input id="quantity" type="number" min="1" name="quantity" value="{{ old('quantity', $don->quantity) }}" class="form-control @error('quantity') is-invalid @enderror">
                @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="donated_at" class="form-label font-weight-bold">Date *</label>
                <input id="donated_at" type="date" name="donated_at" value="{{ old('donated_at', $don->donated_at->format('Y-m-d')) }}" max="{{ date('Y-m-d') }}" class="form-control @error('donated_at') is-invalid @enderror">
                @error('donated_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        
        <div class="mb-3">
            <label for="association_id" class="form-label font-weight-bold">Association *</label>
            <select id="association_id" name="association_id" class="form-select @error('association_id') is-invalid @enderror">
                <option value="">— Choisir une association —</option>
                @foreach ($associations as $a)
                    <option value="{{ $a->id }}" @selected((string) old('association_id', $don->association_id) === (string) $a->id)>{{ $a->name }}@if($a->city) ({{ $a->city }})@endif</option>
                @endforeach
            </select>
            @error('association_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="notes" class="form-label">Remarques (optionnel)</label>
            <textarea id="notes" name="notes" rows="2" class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <button class="btn btn-primary">Envoyer mon don</button>
        <a href="{{ route('dons.index') }}" class="btn btn-link">Annuler</a>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const descInput = document.getElementById('description');
        const qtyInput = document.getElementById('quantity');
        const aiBox = document.getElementById('ai-recommendation');
        const aiResults = document.getElementById('ai-results');
        const assocSelect = document.getElementById('association_id');

        let debounceTimer;

        function fetchSmartMatch() {
            const description = descInput.value.trim();
            const quantity = qtyInput.value || 1;

            // On attend que l'utilisateur tape au moins 5 caractères
            if (description.length < 5) {
                aiBox.classList.add('d-none');
                return;
            }

            // Appel AJAX à notre API Laravel
            fetch("{{ route('api.dons.smart-match') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ description: description, quantity: quantity })
            })
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    aiBox.classList.remove('d-none');
                    aiResults.innerHTML = '';

                    data.forEach(match => {
                        // Création du bouton de recommandation
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'btn btn-sm btn-outline-primary mr-2 mb-2';
                        btn.innerHTML = `<strong>${match.name}</strong> (${match.probability}% de chances)`;
                        
                        // Quand on clique sur la reco, on sélectionne l'association dans le select
                        btn.onclick = () => {
                            assocSelect.value = match.id;
                            // Feedback visuel
                            btn.className = 'btn btn-sm btn-primary mr-2 mb-2';
                        };
                        
                        aiResults.appendChild(btn);
                    });
                } else {
                    aiBox.classList.add('d-none');
                }
            })
            .catch(error => console.error('Erreur API:', error));
        }

        // Écouter la saisie (avec un délai de 800ms pour ne pas spammer le serveur)
        descInput.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(fetchSmartMatch, 800);
        });

        qtyInput.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(fetchSmartMatch, 800);
        });
    });
</script>
@endpush