<?php

namespace App\Notifications;

use App\Models\AdminActivity;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminAccessChanged extends Notification
{
    public function __construct(public AdminActivity $activity) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $a = $this->activity;

        return (new MailMessage)
            ->subject('SSTN: zmena v administrátoroch webu')
            ->greeting('Dobrý deň,')
            ->line('v administrácii webu SSTN nastala zmena v tom, kto má prístup:')
            ->line('**Zmena:** '.$a->label())
            ->line('**Účet:** '.$a->subject_name.' ('.$a->subject_email.')')
            ->line('**Vykonal(a):** '.$a->actorLabel())
            ->line('**Kedy:** '.$a->created_at->timezone(config('app.timezone'))->format('j. n. Y H:i'))
            ->action('Zobraziť administrátorov', route('admin.users.index'))
            ->line('Ak o tejto zmene neviete, ihneď si zmeňte heslo a skontrolujte zoznam administrátorov.')
            ->salutation('SSTN – administrácia webu');
    }
}
