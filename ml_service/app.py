#!/usr/bin/env python3
"""
Service d'estimation de durée — 100 % local (127.0.0.1), aucune API externe.
Lancement : python app.py   (port 8001 par défaut, variable ML_PORT)
"""
import json
import os
from pathlib import Path

import joblib
import numpy as np
import pandas as pd
from flask import Flask, jsonify, request

HERE = Path(__file__).resolve().parent
ART = HERE / "artifacts"
CAT = ["categorie_vetement", "type_intervention", "type_demande"]
NUM = ["niveau_difficulte", "description_len"]

app = Flask(__name__)
_cache = {"sig": None}


def _round5(x: float) -> int:
    return max(5, int(round(float(x) / 5.0) * 5))


def _state() -> dict:
    """Recharge automatiquement les artefacts quand train.py les a modifiés."""
    paths = [ART / "metadata.json", ART / "model.joblib", ART / "reference.json"]
    sig = tuple(p.stat().st_mtime if p.exists() else 0 for p in paths)
    if _cache["sig"] != sig:
        meta = json.loads(paths[0].read_text(encoding="utf-8")) if paths[0].exists() else {}
        ref = json.loads(paths[2].read_text(encoding="utf-8")) if paths[2].exists() else {}
        model = joblib.load(paths[1]) if paths[1].exists() else None
        _cache.update(sig=sig, meta=meta, ref=ref, model=model)
    return _cache


def _validate(d: dict):
    try:
        for c in ("categorie_vetement", "type_intervention"):
            if not isinstance(d.get(c), str) or not d[c]:
                return f"{c} requis"
        if d.get("type_demande") not in ("reparation", "transformation"):
            return "type_demande invalide"
        d["niveau_difficulte"] = int(d.get("niveau_difficulte", 2))
        d["description_len"] = max(0, int(d.get("description_len", 0)))
        if d["niveau_difficulte"] not in (1, 2, 3):
            return "niveau_difficulte invalide"
    except (TypeError, ValueError):
        return "types invalides"
    return None


def _predict_model(st: dict, d: dict) -> dict:
    meta, pipe = st["meta"], st["model"]
    X = pd.DataFrame([{c: d[c] for c in CAT + NUM}])
    Xt = pipe.named_steps["pre"].transform(X)
    trees = np.array([t.predict(Xt)[0] for t in pipe.named_steps["rf"].estimators_])
    mean, p10, p90 = float(trees.mean()), float(np.percentile(trees, 10)), float(np.percentile(trees, 90))
    spread = (p90 - p10) / max(mean, 1.0)
    known = all(str(d[c]) in meta.get("categories", {}).get(c, []) for c in CAT)
    n = int(meta.get("n_samples", 0))
    if not known:
        level = "faible"            # combinaison jamais vue à l'entraînement
    elif spread < 0.35 and n >= 100:
        level = "elevee"
    elif spread < 0.8:
        level = "moyenne"
    else:
        level = "faible"
    return {
        "disponible": True, "mode": "modele",
        "minutes": _round5(mean), "min": _round5(p10), "max": _round5(p90),
        "confiance": level, "n_echantillons": n,
        "mae_minutes": meta.get("mae_minutes"),
        "donnees_demo": int(meta.get("n_demo", 0)) > 0,
        "version": meta.get("version"),
        "message": f"Modèle de machine learning (RandomForest) entraîné localement sur {n} demandes terminées.",
    }


def _predict_reference(st: dict, d: dict):
    ref = st["ref"]
    for key in (f"cat_int|{d['categorie_vetement']}|{d['type_intervention']}", f"int|{d['type_intervention']}"):
        g = ref.get(key)
        if g and g["n"] >= 3:
            return {
                "disponible": True, "mode": "mediane",
                "minutes": _round5(g["median"]), "min": _round5(g["p25"]), "max": _round5(g["p75"]),
                "confiance": "faible", "n_echantillons": g["n"], "donnees_demo": bool(g.get("demo")),
                "message": f"Médiane de {g['n']} demandes similaires terminées — ce n'est PAS un modèle de machine learning.",
            }
    return None


@app.get("/health")
def health():
    st = _state()
    return jsonify({"status": "ok", "modele_charge": st["model"] is not None, "meta": st["meta"]})


@app.post("/predict")
def predict():
    d = request.get_json(silent=True) or {}
    err = _validate(d)
    if err:
        return jsonify({"disponible": False, "raison": "requete_invalide", "message": err}), 422
    st = _state()
    if st["model"] is not None and st["meta"].get("utilisable"):
        return jsonify(_predict_model(st, d))
    res = _predict_reference(st, d)
    if res:
        return jsonify(res)
    n = int(st["meta"].get("n_samples", 0))
    return jsonify({
        "disponible": False, "mode": None, "raison": "donnees_insuffisantes", "n_echantillons": n,
        "message": "Pas encore assez de demandes terminées pour estimer ce type de travail. "
                   "Chaque durée confirmée par un atelier améliore le système.",
    })


if __name__ == "__main__":
    app.run(host="127.0.0.1", port=int(os.getenv("ML_PORT", "8001")))
