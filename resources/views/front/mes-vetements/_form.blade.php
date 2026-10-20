@csrf

{{-- Photo + assistant IA --}}
<div class="mb-4">
    <label class="form-label">Photo du vêtement</label>
    <input type="file" name="image" class="form-control @error('image') is-invalid @enderror"
           accept="image/png,image/jpeg,image/webp">
    @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
    @if(isset($vetement) && $vetement->image)
        <img src="{{ $vetement->imageUrl() }}" width="120" class="mt-3 rounded-4 shadow-sm">
    @endif
</div>

<div id="aiPanel" class="mb-4 p-3 p-md-4 rounded-4"
     style="background: var(--rf-green-soft); border: 1.5px dashed var(--rf-green);"
     data-token="{{ csrf_token() }}"
     data-url-analyser="{{ route('ia.analyser') }}"
     data-url-description="{{ route('ia.description') }}">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h6 class="fw-bold mb-1">✨ Assistant IA</h6>
            <p class="small text-muted mb-0">Ajoutez une photo : l'IA remplit le formulaire et conseille don, réparation ou transformation. Vous pouvez tout corriger.</p>
        </div>
        <button type="button" id="btnAnalyser" class="btn btn-primary btn-sm">
            <i class="bi bi-stars"></i> Analyser la photo
        </button>
    </div>
    <div id="aiResult" class="mt-3 d-none"></div>
</div>

<div class="mb-3">
    <label class="form-label">Nom</label>
    <input name="nom" class="form-control @error('nom') is-invalid @enderror"
           value="{{ old('nom', $vetement->nom ?? '') }}">
    @error('nom') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <div class="d-flex justify-content-between align-items-center mb-1">
        <label class="form-label mb-0">Description</label>
        <button type="button" id="btnDescription" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-magic"></i> Améliorer avec l'IA
        </button>
    </div>
    <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description', $vetement->description ?? '') }}</textarea>
    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="row g-3 mb-3">
    <div class="col-md-3">
        <label class="form-label">Taille</label>
        <input name="taille" class="form-control @error('taille') is-invalid @enderror"
               value="{{ old('taille', $vetement->taille ?? '') }}">
        @error('taille') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">État</label>
        <select name="etat" class="form-select @error('etat') is-invalid @enderror">
            @foreach($etats as $e)
                <option value="{{ $e->value }}" @selected(old('etat', isset($vetement) ? $vetement->etat->value : '') === $e->value)>{{ $e->label() }}</option>
            @endforeach
        </select>
        @error('etat') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">Type</label>
        <select name="type" class="form-select @error('type') is-invalid @enderror">
            @foreach($types as $t)
                <option value="{{ $t->value }}" @selected(old('type', isset($vetement) ? $vetement->type->value : '') === $t->value)>{{ $t->label() }}</option>
            @endforeach
        </select>
        @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">Catégorie</label>
        <select name="categorie_id" class="form-select @error('categorie_id') is-invalid @enderror">
            @foreach($categories as $c)
                <option value="{{ $c->id }}" @selected(old('categorie_id', $vetement->categorie_id ?? '') == $c->id)>{{ $c->nom }}</option>
            @endforeach
        </select>
        @error('categorie_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="d-flex gap-2 mt-4">
    <button class="btn btn-primary btn-lg"><i class="bi bi-check2-circle"></i> Enregistrer</button>
    <a href="{{ route('mes-vetements.index') }}" class="btn btn-outline-secondary btn-lg">Annuler</a>
</div>

@push('styles')
<style>
    .ai-filled { border-color: var(--rf-green) !important; background-color: #f3fcf6 !important; }
</style>
@endpush

@push('scripts')
<script>
(() => {
    const panel = document.getElementById('aiPanel');
    const result = document.getElementById('aiResult');
    const field = (n) => document.querySelector(`[name="${n}"]`);
    const headers = { 'Accept': 'application/json', 'X-CSRF-TOKEN': panel.dataset.token };

    function setValue(name, value) {
        const el = field(name);
        if (!el || value === null || value === undefined || value === '') return;
        el.value = value;
        el.classList.add('ai-filled');
    }

    function message(text, type) {
        result.className = 'mt-3 alert alert-' + type + ' mb-0';
        result.textContent = text;
    }

    function busy(btn, on, label) {
        btn.disabled = on;
        if (on) { btn.dataset.old = btn.innerHTML; btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> ' + label; }
        else { btn.innerHTML = btn.dataset.old; }
    }

    // Fonctionnalités 1 + 2 : analyse de la photo
    document.getElementById('btnAnalyser').addEventListener('click', async (e) => {
        const btn = e.currentTarget;
        const file = field('image').files[0];
        if (!file) return message("Choisissez d'abord une photo.", 'warning');

        const body = new FormData();
        body.append('image', file);
        busy(btn, true, 'Analyse en cours...');
        result.className = 'd-none';

        try {
            const r = await fetch(panel.dataset.urlAnalyser, { method: 'POST', headers, body });
            const d = await r.json();
            if (!r.ok) throw new Error(d.message || (d.errors ? Object.values(d.errors)[0][0] : 'Erreur IA'));

            setValue('nom', d.nom);
            setValue('description', d.description);
            setValue('categorie_id', d.categorie_id);
            setValue('etat', d.etat);
            setValue('type', d.type);

            result.className = 'mt-3 p-3 rounded-3 bg-white border';
            result.replaceChildren();
            const titre = document.createElement('div');
            titre.className = 'fw-bold mb-1';
            titre.textContent = '✅ Formulaire pré-rempli. Vérifiez et corrigez si besoin.';
            result.appendChild(titre);

            if (d.couleur) {
                const c = document.createElement('div'); c.className = 'small';
                c.textContent = 'Couleur : ' + d.couleur; result.appendChild(c);
            }
            const def = document.createElement('div'); def.className = 'small';
            def.textContent = d.defauts.length ? 'Défauts repérés : ' + d.defauts.join(', ') : 'Aucun défaut visible repéré.';
            result.appendChild(def);
            if (d.raison) {
                const rs = document.createElement('div'); rs.className = 'small text-success fw-semibold mt-1';
                rs.textContent = '💡 ' + d.raison; result.appendChild(rs);
            }
        } catch (err) {
            message(err.message, 'danger');
        } finally {
            busy(btn, false);
        }
    });

    // Fonctionnalité 5 : amélioration de la description
    document.getElementById('btnDescription').addEventListener('click', async (e) => {
        const btn = e.currentTarget;
        if (!field('nom').value.trim()) return message("Saisissez d'abord le nom du vêtement.", 'warning');

        busy(btn, true, 'Rédaction...');
        result.className = 'd-none';

        try {
            const r = await fetch(panel.dataset.urlDescription, {
                method: 'POST',
                headers: { ...headers, 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    nom: field('nom').value,
                    description: field('description').value,
                    categorie_id: field('categorie_id').value,
                    etat: field('etat').value,
                    taille: field('taille').value,
                }),
            });
            const d = await r.json();
            if (!r.ok) throw new Error(d.message || (d.errors ? Object.values(d.errors)[0][0] : 'Erreur IA'));
            setValue('description', d.description);
        } catch (err) {
            message(err.message, 'danger');
        } finally {
            busy(btn, false);
        }
    });
})();
</script>
@endpush