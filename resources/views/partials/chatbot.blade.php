<style>
    #tc-chat-btn { position: fixed; right: 20px; bottom: 20px; z-index: 9998; width: 58px; height: 58px; border-radius: 50%;
        border: 0; background: #28a745; color: #fff; font-size: 26px; cursor: pointer; box-shadow: 0 4px 14px rgba(0,0,0,.3); }
    #tc-chat { position: fixed; right: 20px; bottom: 90px; z-index: 9999; width: 370px; max-width: calc(100vw - 30px); height: 520px;
        max-height: calc(100vh - 120px); background: #fff; border-radius: 14px; box-shadow: 0 8px 30px rgba(0,0,0,.3);
        display: none; flex-direction: column; overflow: hidden; font-family: Arial, sans-serif; font-size: 14px; color: #222; }
    #tc-chat.ouvert { display: flex; }
    #tc-chat header { background: #28a745; color: #fff; padding: 12px 14px; font-weight: bold; display: flex; justify-content: space-between; }
    #tc-chat header span { cursor: pointer; }
    #tc-msgs { flex: 1; overflow-y: auto; padding: 12px; background: #f5f6f7; }
    .tc-m { max-width: 88%; margin-bottom: 10px; padding: 8px 12px; border-radius: 12px; white-space: pre-wrap; line-height: 1.4; }
    .tc-user { background: #28a745; color: #fff; margin-left: auto; border-bottom-right-radius: 3px; }
    .tc-bot { background: #fff; border: 1px solid #e1e1e1; border-bottom-left-radius: 3px; }
    .tc-filtres { font-size: 11px; color: #666; margin: -4px 0 8px 2px; }
    .tc-carte { display: flex; gap: 10px; background: #fff; border: 1px solid #e1e1e1; border-radius: 10px; padding: 8px;
        margin-bottom: 8px; text-decoration: none; color: inherit; }
    .tc-carte:hover { border-color: #28a745; }
    .tc-carte img, .tc-carte .tc-ph { width: 56px; height: 56px; border-radius: 8px; object-fit: cover; background: #e9ecef;
        display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0; }
    .tc-carte b { display: block; font-size: 13px; }
    .tc-carte small { color: #666; }
    #tc-sugg { padding: 6px 10px; display: flex; flex-wrap: wrap; gap: 6px; background: #f5f6f7; }
    #tc-sugg button { border: 1px solid #28a745; background: #fff; color: #28a745; border-radius: 14px; padding: 3px 10px;
        font-size: 12px; cursor: pointer; }
    #tc-form { display: flex; border-top: 1px solid #ddd; }
    #tc-input { flex: 1; border: 0; padding: 12px; outline: none; font-size: 14px; }
    #tc-form button { border: 0; background: #28a745; color: #fff; padding: 0 16px; cursor: pointer; font-size: 16px; }
    #tc-form button:disabled { background: #9bd0a9; }
</style>

<button id="tc-chat-btn" type="button" title="Assistant TextileCycle">💬</button>

<div id="tc-chat" role="dialog" aria-label="Assistant TextileCycle">
    <header>Assistant TextileCycle <span id="tc-fermer">✕</span></header>
    <div id="tc-msgs"></div>
    <div id="tc-sugg">
        <button type="button">Je cherche un manteau taille L en bon état à donner</button>
        <button type="button">Ma veste est déchirée, que faire ?</button>
    </div>
    <form id="tc-form" autocomplete="off">
        <input id="tc-input" type="text" maxlength="500" placeholder="Décrivez ce que vous cherchez…" required>
        <button type="submit">➤</button>
    </form>
</div>

<script>
(function () {
    const URL_CHAT = @json(route('assistant.chat'));
    const CSRF = @json(csrf_token());
    const fenetre = document.getElementById('tc-chat');
    const msgs = document.getElementById('tc-msgs');
    const form = document.getElementById('tc-form');
    const input = document.getElementById('tc-input');
    const bouton = form.querySelector('button');
    let historique = [];

    document.getElementById('tc-chat-btn').onclick = () => {
        fenetre.classList.toggle('ouvert');
        if (fenetre.classList.contains('ouvert') && !msgs.children.length) {
            ajouter('bot', "Bonjour ! Dites-moi quel vêtement vous cherchez (catégorie, taille, état…) ou décrivez un problème, je vous aide.");
        }
        input.focus();
    };
    document.getElementById('tc-fermer').onclick = () => fenetre.classList.remove('ouvert');

    document.querySelectorAll('#tc-sugg button').forEach(b => b.onclick = () => envoyer(b.textContent));

    form.onsubmit = e => { e.preventDefault(); envoyer(input.value.trim()); };

    function ajouter(qui, texte) {
        const d = document.createElement('div');
        d.className = 'tc-m tc-' + (qui === 'user' ? 'user' : 'bot');
        d.textContent = texte;
        msgs.appendChild(d);
        msgs.scrollTop = msgs.scrollHeight;
        return d;
    }

    function cartes(liste) {
        liste.forEach(v => {
            const a = document.createElement('a');
            a.className = 'tc-carte'; a.href = v.url;
            const visuel = v.image ? Object.assign(document.createElement('img'), { src: v.image, alt: v.nom })
                                   : Object.assign(document.createElement('div'), { className: 'tc-ph', textContent: '👕' });
            const txt = document.createElement('div');
            const b = document.createElement('b'); b.textContent = v.nom;
            const s = document.createElement('small');
            s.textContent = [v.categorie, 'Taille ' + v.taille, v.etat, v.type].filter(Boolean).join(' · ');
            txt.append(b, s);
            a.append(visuel, txt);
            msgs.appendChild(a);
        });
        msgs.scrollTop = msgs.scrollHeight;
    }

    async function envoyer(texte) {
        if (!texte) return;
        document.getElementById('tc-sugg').style.display = 'none';
        ajouter('user', texte);
        input.value = ''; bouton.disabled = true; input.disabled = true;
        const attente = ajouter('bot', '…');

        try {
            const r = await fetch(URL_CHAT, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ message: texte, historique })
            });
            const data = await r.json();
            attente.remove();

            if (!r.ok) {
                ajouter('bot', data.message || 'Une erreur est survenue, réessayez.');
            } else {
                ajouter('bot', data.reponse);
                const f = Object.entries(data.filtres || {}).map(([k, v]) => k + ' = ' + v).join(' · ');
                if (f) { const d = document.createElement('div'); d.className = 'tc-filtres'; d.textContent = '🔎 Filtres compris : ' + f; msgs.appendChild(d); }
                cartes(data.vetements || []);
                historique.push({ role: 'user', text: texte }, { role: 'assistant', text: data.reponse });
                historique = historique.slice(-10);
            }
        } catch (e) {
            attente.remove();
            ajouter('bot', "Connexion impossible. Vérifiez votre réseau et réessayez.");
        }
        bouton.disabled = false; input.disabled = false; input.focus();
    }
})();
</script>