<?php

namespace App\Exceptions;

use RuntimeException;

/** Erreur "propre" de l'assistant IA : le message est affichable à l'utilisateur. */
class AiException extends RuntimeException
{
}