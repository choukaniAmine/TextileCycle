<?php

namespace App\Services\Grok;

use RuntimeException;

class GrokException extends RuntimeException
{
    public function __construct(private string $userMessage, private int $httpStatus = 502, string $internal = '')
    {
        parent::__construct($internal !== '' ? $internal : $userMessage);
    }

    public function userMessage(): string
    {
        return $this->userMessage;
    }

    public function httpStatus(): int
    {
        return $this->httpStatus;
    }

    public static function notConfigured(): self
    {
        return new self('L’assistant n’est pas configuré pour le moment.', 503, 'XAI_API_KEY manquante');
    }

    public static function auth(int $status): self
    {
        return new self('L’assistant est momentanément indisponible.', 503, "xAI a refusé l'authentification (HTTP {$status})");
    }

    public static function rateLimited(?string $retryAfter): self
    {
        $suffix = $retryAfter && ctype_digit($retryAfter) ? " Réessayez dans {$retryAfter} s." : ' Réessayez dans un instant.';

        return new self('L’assistant reçoit trop de demandes.'.$suffix, 429, 'xAI a répondu 429');
    }

    public static function unavailable(string $internal): self
    {
        return new self('L’assistant est momentanément indisponible. Vous pouvez remplir le formulaire manuellement.', 503, $internal);
    }

    public static function timeout(): self
    {
        return new self('L’assistant met trop de temps à répondre. Réessayez ou remplissez le formulaire manuellement.', 504, 'Délai dépassé ou connexion impossible');
    }

    public static function invalidResponse(string $internal): self
    {
        return new self('La réponse de l’assistant n’a pas pu être exploitée. Reformulez votre description.', 502, $internal);
    }
}
