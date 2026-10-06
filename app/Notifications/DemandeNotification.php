<?php

namespace App\Notifications;

use App\Models\Demande;
use Illuminate\Notifications\Notification;

/** Notification "in-app" (table notifications) liée à une demande. */
class DemandeNotification extends Notification
{
    public function __construct(
        public Demande $demande,
        public string $message,
        public string $icon = '🧵',
        public ?string $url = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'demande_id' => $this->demande->id,
            'titre' => $this->demande->titre,
            'message' => $this->message,
            'icon' => $this->icon,
            'url' => $this->url ?? route('demandes.show', $this->demande, false),
        ];
    }
}
