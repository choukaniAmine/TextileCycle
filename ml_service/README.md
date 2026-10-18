# Service d'estimation de durée (IA n°1 – 100 % local)

Aucune API externe. Python + scikit-learn, écoute uniquement sur 127.0.0.1:8001.

## Installation (Windows)
```bat
cd ml_service
python -m venv .venv
.venv\Scripts\activate
pip install -r requirements.txt
```

## Cycle d'utilisation
1. Les ateliers confirment la **durée réelle** des travaux (page « Mes travaux »).
2. `php artisan ml:train` exporte les demandes terminées vers `storage/app/ml/durees.csv`, puis lance `train.py`.
3. `python app.py` démarre le service (il recharge seul le modèle après chaque entraînement).

## Stratégie de démarrage (honnête)
| Situation | Ce que le système fait |
|---|---|
| < 3 demandes terminées similaires | « Estimation indisponible » — rien n'est inventé |
| ≥ 3 demandes similaires, < 30 au total | Médiane de ces demandes, étiquetée « pas du ML », confiance faible |
| ≥ 30 demandes terminées | Entraînement d'un RandomForest ; utilisé **seulement** s'il bat la médiane globale d'au moins 10 % en validation croisée |
| Données de démonstration présentes | Avertissement affiché dans l'interface |

Limites : l'estimation ne tient compte que du type de vêtement, du type d'intervention, de l'ampleur
déclarée, du type de demande et de la longueur de la description. Elle ignore l'habileté de l'atelier,
la matière du tissu, etc. Elle est indicative et l'atelier reste maître de la durée réelle.

## Données de démonstration
`php artisan db:seed --class=DemoDureesSeeder` crée 90 demandes aux durées SIMULÉES (marquées `demo`).
Entraînement sur données réelles uniquement : `php artisan ml:train --reel-seulement`.
