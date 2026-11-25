<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminInvitation extends Notification
{
    public function __construct(public string $token, public string $inviterName) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $days = intdiv(config('auth.passwords.invites.expire'), 60 * 24);

        return (new MailMessage)
            ->subject('Pozvánka do administrácie webu SSTN')
            ->greeting('Dobrý deň,')
            ->line($this->inviterName.' vás pozýva do administrácie webu Slovenskej spoločnosti pre technickú normalizáciu.')
            ->line('Kliknutím na tlačidlo si nastavíte heslo. Potom sa prihlásite e-mailom '.$notifiable->getEmailForPasswordReset().'.')
            ->action('Nastaviť heslo', route('invitation.show', ['token' => $this->token, 'email' => $notifiable->getEmailForPasswordReset()]))
            ->line("Odkaz platí {$days} dní a dá sa použiť len raz.")
            ->line('Ak pozvánku nečakáte, tento e-mail ignorujte.')
            ->salutation('SSTN – administrácia webu');
    }
}
