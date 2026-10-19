<?php

return [
    /*
    | IA n°1 — service d'estimation LOCAL (Python, scikit-learn). Aucune API externe.
    */
    'ml' => [
        'url' => env('ML_SERVICE_URL', 'http://127.0.0.1:8001'),
        'connect_timeout' => 1,
        'timeout' => 3,
        'python' => env('ML_PYTHON_BIN', 'python'),
    ],

    /*
    | IA n°2 — assistant Grok (xAI). La clé reste dans .env et n'est lue que côté serveur.
    | API recommandée par la documentation xAI : POST {base_url}/responses
    | Modèles disponibles : https://docs.x.ai/developers/models
    */
   'grok' => [
        'key' => env('XAI_API_KEY'),
        'base_url' => env('XAI_BASE_URL', 'https://api.groq.com/openai/v1'),
        'model' => env('XAI_MODEL', 'openai/gpt-oss-20b'),
        'connect_timeout' => 5,
        'timeout' => (int) env('XAI_TIMEOUT', 30),
        'max_output_tokens' => 1500,
        'rate_limit_per_minute' => (int) env('XAI_RATE_LIMIT', 6),
    ],
];
