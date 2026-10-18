{{-- Assistant intelligent (IA n°2, Grok). Analyse seulement : l'utilisateur confirme lui-même. --}}
@once
    @push('styles')<link rel="stylesheet" href="{{ asset('css/ai.css') }}">@endpush
@endonce

<div class="ai-panel" id="ia-assistant" data-url="{{ route('demandes.assistant') }}">
    <h6>✨ Assistant de réparation</h6>
    <p class="hint mb-2">Décrivez votre problème avec vos mots : l’assistant propose des solutions et prépare le formulaire. Vous vérifiez tout avant d’envoyer.</p>
    <textarea id="ia-texte" rows="3" maxlength="1500" class="form-control"
              placeholder="Ex : Mon jean est déchiré au niveau du genou. Je veux le réparer sans changer son apparence et sans dépenser beaucoup."></textarea>
    <div class="d-flex align-items-center gap-3 mt-2">
        <button type="button" id="ia-analyser" class="btn btn-success btn-sm">🔍 Analyser ma description</button>
        <span class="ai-spinner" id="ia-spinner">Analyse en cours…</span>
    </div>
    <div class="hint mt-2">Le texte saisi ici est envoyé à un service d’IA externe (xAI) pour l’analyse. N’y indiquez pas d’informations personnelles.</div>
    <div class="ai-error" id="ia-erreur" role="alert"></div>
    <div class="ai-result" id="ia-resultat"></div>
</div>

@push('scripts')
<script>
(function () {
    const root = document.getElementById('ia-assistant');
    if (!root) return;
    const $ = id => document.getElementById(id);
    const el = (tag, cls, txt) => { const e = document.createElement(tag); if (cls) e.className = cls; if (txt !== undefined && txt !== null) e.textContent = txt; return e; };
    const CATS = @json(config('repair.categories'));
    const INTS = @json(config('repair.interventions'));
    const NIV = @json(config('repair.niveaux'));
    const COUT = {faible: 'coût faible', moyen: 'coût moyen', eleve: 'coût élevé'};
    let analyse = null;

    function error(msg) { const e = $('ia-erreur'); e.textContent = msg; e.classList.add('on'); }
    function clearError() { $('ia-erreur').classList.remove('on'); }

    function setField(name, value) {
        const f = document.querySelector('[name="' + name + '"]:not([type=radio])');
        if (f) { f.value = value; f.dispatchEvent(new Event('input', {bubbles: true})); f.dispatchEvent(new Event('change', {bubbles: true})); }
    }
    function setRadio(name, value) {
        const r = document.querySelector('[name="' + name + '"][value="' + value + '"]');
        if (r) { r.checked = true; r.dispatchEvent(new Event('change', {bubbles: true})); }
    }

    function prefill() {
        if (!analyse) return;
        if (analyse.type_demande) setRadio('type', analyse.type_demande);
        if (analyse.titre_suggere) setField('titre', analyse.titre_suggere);
        if (analyse.type_vetement) setField('vetement', analyse.type_vetement);
        if (analyse.description_suggeree) setField('description', analyse.description_suggeree);
        if (analyse.categorie_vetement) setField('categorie_vetement', analyse.categorie_vetement);
        if (analyse.type_intervention) setField('type_intervention', analyse.type_intervention);
        if (analyse.niveau_difficulte) setRadio('niveau_difficulte', analyse.niveau_difficulte);
        document.getElementById('titre')?.scrollIntoView({behavior: 'smooth', block: 'center'});
        const note = el('div', 'ai-missing', '✅ Formulaire prérempli. Relisez et corrigez les champs, puis cliquez sur « Envoyer ma demande » : rien n’est envoyé automatiquement.');
        $('ia-resultat').append(note);
    }

    function render(a) {
        const out = $('ia-resultat');
        out.replaceChildren();
        const chips = el('div', 'ai-chips');
        if (a.type_vetement) chips.append(el('span', 'ai-chip', '👕 ' + a.type_vetement));
        if (a.categorie_vetement) chips.append(el('span', 'ai-chip', 'Catégorie : ' + (CATS[a.categorie_vetement] || a.categorie_vetement)));
        if (a.type_intervention) chips.append(el('span', 'ai-chip', 'Intervention : ' + (INTS[a.type_intervention] || a.type_intervention)));
        if (a.niveau_difficulte) chips.append(el('span', 'ai-chip', 'Ampleur : ' + (NIV[a.niveau_difficulte] || a.niveau_difficulte)));
        out.append(chips);
        if (a.probleme) out.append(el('p', 'mb-2', '🔎 Problème probable : ' + a.probleme));

        (a.solutions || []).forEach(s => {
            const c = el('div', 'sol-card');
            c.append(el('strong', '', s.titre));
            if (s.explication) c.append(el('div', 'small', s.explication));
            if ((s.avantages || []).length) { const ul = el('ul'); s.avantages.forEach(v => ul.append(el('li', '', v))); c.append(ul); }
            const meta = [COUT[s.cout_relatif], s.difficulte ? 'difficulté ' + s.difficulte : null].filter(Boolean).join(' · ');
            if (meta) c.append(el('div', 'sol-meta mt-1', meta));
            out.append(c);
        });

        if ((a.informations_manquantes || []).length) {
            const m = el('div', 'ai-missing', 'Pour être plus précis, il manque :');
            const ul = el('ul'); a.informations_manquantes.forEach(v => ul.append(el('li', '', v))); m.append(ul);
            out.append(m);
        }

        const btn = el('button', 'btn btn-success btn-sm', '📝 Préremplir le formulaire avec ces informations');
        btn.type = 'button';
        btn.addEventListener('click', prefill);
        out.append(btn);
        out.classList.add('on');
    }

    $('ia-analyser').addEventListener('click', async () => {
        clearError();
        const texte = $('ia-texte').value.trim();
        if (texte.length < 15) { error('Décrivez un peu plus votre problème (15 caractères minimum).'); return; }
        const btn = $('ia-analyser');
        btn.disabled = true; $('ia-spinner').classList.add('on'); $('ia-resultat').classList.remove('on');
        try {
            const res = await fetch(root.dataset.url, {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value || ''},
                body: JSON.stringify({description: texte}),
            });
            const data = await res.json().catch(() => ({}));
            if (!res.ok || !data.ok) { error(data.message || 'L’assistant n’a pas pu répondre. Remplissez le formulaire manuellement.'); return; }
            analyse = data.analyse;
            render(analyse);
        } catch (e) {
            error('Connexion impossible. Vérifiez votre réseau ou remplissez le formulaire manuellement.');
        } finally {
            btn.disabled = false; $('ia-spinner').classList.remove('on');
        }
    });
})();
</script>
@endpush
