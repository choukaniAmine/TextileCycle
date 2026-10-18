{{-- Précisions techniques + estimation IA locale --}}

@once
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/ai.css') }}">
    @endpush
@endonce

<hr class="form-divider">

<div class="form-section-title">
    <span class="num">✂️</span>
    Précisions pour estimer le temps de travail
</div>

{{-- Type de vêtement et type d'intervention --}}
<div class="row">

    <div class="col-md-6 mb-3">
        <label class="form-label" for="categorie_vetement">
            Type de vêtement
        </label>

        <select
            id="categorie_vetement"
            name="categorie_vetement"
            class="form-select @error('categorie_vetement') is-invalid @enderror"
        >
            <option value="">— Choisir un vêtement —</option>

            @foreach (config('repair.categories') as $key => $label)
                <option
                    value="{{ $key }}"
                    @selected(old('categorie_vetement', $demande->categorie_vetement) === $key)
                >
                    {{ $label }}
                </option>
            @endforeach
        </select>

        @error('categorie_vetement')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label" for="type_intervention">
            Type d’intervention
        </label>

        <select
            id="type_intervention"
            name="type_intervention"
            class="form-select @error('type_intervention') is-invalid @enderror"
        >
            <option value="">— Choisir une intervention —</option>

            @foreach (config('repair.interventions') as $key => $label)
                <option
                    value="{{ $key }}"
                    @selected(old('type_intervention', $demande->type_intervention) === $key)
                >
                    {{ $label }}
                </option>
            @endforeach
        </select>

        @error('type_intervention')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

</div>

{{-- Niveau de difficulté --}}
<div class="mb-4">

    <label class="form-label d-block">
        Ampleur du dommage / du travail
    </label>

    <div class="niveau-pills">

        @foreach (config('repair.niveaux') as $n => $label)
            <label class="niveau-pill">

                <input
                    type="radio"
                    name="niveau_difficulte"
                    value="{{ $n }}"
                    @checked((int) old('niveau_difficulte', $demande->niveau_difficulte ?? 2) === $n)
                >

                <span>{{ $label }}</span>

            </label>
        @endforeach

    </div>

    @error('niveau_difficulte')
        <div class="text-danger small mt-2">
            {{ $message }}
        </div>
    @enderror

</div>

{{-- Carte d'estimation IA --}}
<div
    id="ia-estimation"
    class="est-box empty"
    data-url="{{ route('demandes.estimation') }}"
    aria-live="polite"
    aria-atomic="true"
>

    <div class="est-loading-icon" aria-hidden="true">
        ⏱️
    </div>

    <div class="est-initial-content">

        <strong>
            Estimation intelligente du temps de travail
        </strong>

        <p>
            Choisissez le vêtement et le type d’intervention
            pour découvrir la durée estimée par notre intelligence artificielle.
        </p>

    </div>

</div>

@push('scripts')
<script>
(function () {
    'use strict';

    const box = document.getElementById('ia-estimation');

    if (!box) {
        return;
    }

    const token = () =>
        document.querySelector('input[name="_token"]')?.value || '';

    const val = (name) =>
        document.querySelector('[name="' + name + '"]')?.value || '';

    const checked = (name) =>
        document.querySelector('[name="' + name + '"]:checked')?.value || '';

    const fmt = (minutes) => {
        minutes = Math.round(Number(minutes) || 0);

        const hours = Math.floor(minutes / 60);
        const remaining = minutes % 60;

        if (hours === 0) {
            return minutes + ' min';
        }

        if (remaining === 0) {
            return hours + ' h';
        }

        return hours + ' h ' + String(remaining).padStart(2, '0');
    };

    const el = (tag, cls, txt) => {
        const element = document.createElement(tag);

        if (cls) {
            element.className = cls;
        }

        if (txt !== undefined) {
            element.textContent = txt;
        }

        return element;
    };

    const CONF = {
        faible: 'Confiance faible',
        moyenne: 'Confiance moyenne',
        elevee: 'Confiance élevée'
    };

    const MODE = {
        modele: 'Modèle de machine learning',
        mediane: 'Médiane historique'
    };

    let timer;
    let seq = 0;

    /*
     * Affiche l'estimation reçue du serveur.
     * Les valeurs restent dynamiques.
     */
    function show(result) {

        box.replaceChildren();

        box.classList.remove('empty', 'est-error', 'est-result');

        if (!result.disponible) {

            box.classList.add('empty', 'est-error');

            const icon = el('div', 'est-loading-icon', '⚠️');

            const content = el('div', 'est-initial-content');

            content.append(
                el('strong', '', 'Estimation momentanément indisponible')
            );

            content.append(
                el(
                    'p',
                    '',
                    result.message || 'Le service d’estimation est momentanément indisponible.'
                )
            );

            box.append(icon, content);

            return;
        }

        box.classList.add('est-result');

        /*
         * En-tête de la carte
         */
        const header = el('div', 'est-header');

        const headerIcon = el('div', 'est-header-icon', '✂️');

        const headerContent = el('div', 'est-header-content');

        headerContent.append(
            el('span', 'est-eyebrow', 'RE-FASHION INTELLIGENCE')
        );

        headerContent.append(
            el('h3', 'est-title', 'Votre estimation')
        );

        header.append(headerIcon, headerContent);

        /*
         * Durée estimée
         */
        const main = el('div', 'est-main');

        main.append(
            el('span', 'est-main-label', 'Temps de travail estimé')
        );

        main.append(
            el('div', 'est-main-value', '≈ ' + fmt(result.minutes))
        );

        /*
         * Fourchette de durée
         */
        const range = el('div', 'est-range-card');

        const rangeIcon = el('div', 'est-range-icon', '◷');

        const rangeContent = el('div', 'est-range-content');

        rangeContent.append(
            el('span', 'est-range-label', 'Fourchette probable')
        );

        rangeContent.append(
            el(
                'strong',
                'est-range-value',
                fmt(result.min) + ' – ' + fmt(result.max)
            )
        );

        range.append(rangeIcon, rangeContent);

        /*
         * Badges : confiance et méthode
         */
        const badges = el('div', 'est-badges');

        const confidence = result.confiance || 'faible';

        const confidenceBadge = el(
            'span',
            'badge-conf ' + confidence
        );

        confidenceBadge.append(
            el('span', 'badge-dot')
        );

        confidenceBadge.append(
            document.createTextNode(
                CONF[confidence] || 'Confiance non précisée'
            )
        );

        const modeBadge = el(
            'span',
            'badge-mode',
            '✦ ' + (MODE[result.mode] || 'Estimation automatique')
        );

        badges.append(confidenceBadge, modeBadge);

        /*
         * Message du modèle
         */
        const message = null

        /*
         * Avertissement pour les données de démonstration
         */
        let warning = null;

        if (result.donnees_demo) {

            warning = el('div', 'est-warn');


            const warningContent = el('div', 'est-warn-content');

          

           

            warning.append(warningContent);
        }

        /*
         * Note finale
         */
        const footer = el('div', 'est-footer');

        footer.append(
            el('span', 'est-footer-icon', 'ℹ️')
        );

        footer.append(
            el(
                'p',
                '',
                'Cette estimation est indicative. L’atelier confirmera la durée réelle du travail.'
            )
        );

        /*
         * Assemblage de la carte
         */
        box.append(header);
        box.append(main);
        box.append(range);
        box.append(badges);
      //  box.append(message);


        box.append(footer);
    }

    /*
     * Envoie les informations à Laravel
     * pour obtenir une estimation.
     */
    async function run() {

        const payload = {
            categorie_vetement: val('categorie_vetement'),
            type_intervention: val('type_intervention'),
            type_demande: checked('type') || 'reparation',
            niveau_difficulte: parseInt(
                checked('niveau_difficulte') || '2',
                10
            ),
            description: val('description')
        };

        if (
            !payload.categorie_vetement ||
            !payload.type_intervention
        ) {
            return;
        }

        const currentRequest = ++seq;

        /*
         * État de chargement
         */
        box.replaceChildren();

        box.classList.remove('est-result', 'est-error');
        box.classList.add('empty');

        const loadingIcon = el('div', 'est-loading-icon', '⏳');

        const loadingContent = el('div', 'est-initial-content');

        loadingContent.append(
            el('strong', '', 'Calcul de votre estimation...')
        );

        loadingContent.append(
            el(
                'p',
                '',
                'Notre modèle analyse les caractéristiques de votre demande.'
            )
        );

        box.append(loadingIcon, loadingContent);

        try {

            const response = await fetch(box.dataset.url, {
                method: 'POST',

                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token()
                },

                body: JSON.stringify(payload)
            });

            const data = await response.json();

            /*
             * Ignore les réponses anciennes si l'utilisateur
             * modifie rapidement les champs.
             */
            if (currentRequest !== seq) {
                return;
            }

            if (!response.ok) {

                show({
                    disponible: false,
                    message: data.message || 'Impossible de calculer l’estimation.'
                });

                return;
            }

            show(data);

        } catch (error) {

            if (currentRequest !== seq) {
                return;
            }

            show({
                disponible: false,
                message: 'Connexion impossible au service d’estimation. Vérifiez que le serveur est démarré.'
            });
        }
    }

    /*
     * Évite d'envoyer une requête à chaque frappe.
     */
    const schedule = () => {
        clearTimeout(timer);
        timer = setTimeout(run, 500);
    };

    /*
     * Écoute les modifications des champs.
     */
    [
        'categorie_vetement',
        'type_intervention',
        'description'
    ].forEach(name => {

        document
            .querySelector('[name="' + name + '"]')
            ?.addEventListener('input', schedule);

    });

    document.querySelectorAll(
        '[name="type"], [name="niveau_difficulte"], ' +
        '[name="categorie_vetement"], [name="type_intervention"]'
    ).forEach(element => {

        element.addEventListener('change', schedule);

    });

    /*
     * Lance une première estimation si les champs
     * sont déjà renseignés.
     */
    run();

})();
</script>
@endpush