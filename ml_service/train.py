#!/usr/bin/env python3
"""
Entraînement du modèle LOCAL d'estimation de durée (scikit-learn, aucune API externe).

Entrée  : un CSV exporté par Laravel (php artisan ml:train) avec une ligne par demande TERMINÉE
          dont l'atelier a confirmé la durée réelle.
Sortie  : artifacts/model.joblib (si assez de données), artifacts/metadata.json, artifacts/reference.json

Honnêteté sur les données :
  * en dessous de --min-samples lignes, AUCUN modèle n'est entraîné ;
  * un modèle n'est déclaré « utilisable » que s'il bat une base de référence simple
    (prédire la médiane globale) en validation croisée ;
  * les lignes de démonstration (source = demo) sont comptées à part et signalées dans les métadonnées.
"""
import argparse
import json
import sys
from datetime import datetime, timezone
from pathlib import Path

import joblib
import numpy as np
import pandas as pd
from sklearn.compose import ColumnTransformer
from sklearn.ensemble import RandomForestRegressor
from sklearn.metrics import mean_absolute_error, r2_score
from sklearn.model_selection import KFold, cross_val_predict
from sklearn.pipeline import Pipeline
from sklearn.preprocessing import OneHotEncoder

HERE = Path(__file__).resolve().parent
ART = HERE / "artifacts"
CAT = ["categorie_vetement", "type_intervention", "type_demande"]
NUM = ["niveau_difficulte", "description_len"]
TARGET = "duree_reelle_min"


def load(csv_path: Path, exclude_demo: bool) -> pd.DataFrame:
    df = pd.read_csv(csv_path)
    missing = [c for c in CAT + NUM + [TARGET] if c not in df.columns]
    if missing:
        sys.exit(f"Colonnes manquantes dans le CSV : {missing}")
    if "source" not in df.columns:
        df["source"] = "atelier"
    df = df.dropna(subset=CAT + NUM + [TARGET])
    df = df[(df[TARGET] >= 5) & (df[TARGET] <= 10080)]  # entre 5 min et 7 jours
    if exclude_demo:
        df = df[df["source"] != "demo"]
    return df.reset_index(drop=True)


def reference_stats(df: pd.DataFrame) -> dict:
    """Médianes historiques par groupe (réelles, pas du ML) : utilisées si le modèle n'existe pas."""
    out = {}
    for keys, prefix in ((["categorie_vetement", "type_intervention"], "cat_int"), (["type_intervention"], "int")):
        for k, g in df.groupby(keys):
            k = k if isinstance(k, tuple) else (k,)
            out[prefix + "|" + "|".join(map(str, k))] = {
                "n": int(len(g)),
                "median": float(g[TARGET].median()),
                "p25": float(g[TARGET].quantile(0.25)),
                "p75": float(g[TARGET].quantile(0.75)),
                "demo": bool((g["source"] == "demo").any()),
            }
    return out


def build_model() -> Pipeline:
    pre = ColumnTransformer(
        [
            ("cat", OneHotEncoder(handle_unknown="ignore", sparse_output=False), CAT),
            ("num", "passthrough", NUM),
        ],
        sparse_threshold=0,
    )
    rf = RandomForestRegressor(n_estimators=300, min_samples_leaf=2, random_state=42, n_jobs=-1)
    return Pipeline([("pre", pre), ("rf", rf)])


def main() -> None:
    ap = argparse.ArgumentParser()
    ap.add_argument("--csv", default=str(HERE.parent / "storage" / "app" / "ml" / "durees.csv"))
    ap.add_argument("--min-samples", type=int, default=30)
    ap.add_argument("--exclude-demo", action="store_true", help="ignorer les données de démonstration")
    args = ap.parse_args()

    ART.mkdir(exist_ok=True)
    df = load(Path(args.csv), args.exclude_demo)
    n, n_demo = len(df), int((df["source"] == "demo").sum())
    meta = {
        "trained": False, "utilisable": False, "min_samples": args.min_samples,
        "n_samples": n, "n_demo": n_demo, "n_real": n - n_demo,
        "trained_at": datetime.now(timezone.utc).isoformat(timespec="seconds"),
    }

    (ART / "reference.json").write_text(json.dumps(reference_stats(df), ensure_ascii=False, indent=2), encoding="utf-8")
    model_path = ART / "model.joblib"
    if model_path.exists():
        model_path.unlink()  # jamais de modèle périmé

    print(f"{n} demandes terminées exploitables ({n_demo} de démonstration, {n - n_demo} réelles).")

    if n < args.min_samples:
        meta["message"] = f"Pas assez de données pour entraîner un modèle ({n} < {args.min_samples})."
        (ART / "metadata.json").write_text(json.dumps(meta, ensure_ascii=False, indent=2), encoding="utf-8")
        print("➜ " + meta["message"] + " Seules les médianes de demandes similaires seront proposées.")
        return

    X, y = df[CAT + NUM], df[TARGET].astype(float)
    cv = KFold(n_splits=min(5, n), shuffle=True, random_state=42)
    pred = cross_val_predict(build_model(), X, y, cv=cv)
    mae = float(mean_absolute_error(y, pred))
    baseline = float(np.mean(np.abs(y - y.median())))
    r2 = float(r2_score(y, pred))

    model = build_model().fit(X, y)
    joblib.dump(model, model_path)

    meta.update(
        trained=True,
        utilisable=bool(mae < 0.9 * baseline),  # doit battre la base de référence d'au moins 10 %
        version=datetime.now().strftime("%Y%m%d%H%M%S"),
        mae_minutes=round(mae, 1),
        mae_baseline_minutes=round(baseline, 1),
        r2_cv=round(r2, 3),
        categories={c: sorted(df[c].astype(str).unique().tolist()) for c in CAT},
        message="Modèle RandomForest entraîné localement (validation croisée 5 plis).",
    )
    (ART / "metadata.json").write_text(json.dumps(meta, ensure_ascii=False, indent=2), encoding="utf-8")
    print(f"Erreur moyenne (CV) : {mae:.1f} min  |  base de référence (médiane) : {baseline:.1f} min  |  R² : {r2:.2f}")
    print("➜ Modèle " + ("UTILISABLE." if meta["utilisable"] else "NON utilisable (ne bat pas la base de référence) : médianes proposées à la place."))


if __name__ == "__main__":
    main()
