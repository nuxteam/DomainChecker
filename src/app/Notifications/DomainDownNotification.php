<?php
namespace App\Notifications;

use App\Models\Domain;
use App\Models\DomainCheck;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DomainDownNotification extends Notification
{
    public function __construct(
        public Domain $domain,
        public DomainCheck $check
    ) {}

    public function via($notifiable): array { return ['mail']; }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("⚠️ DOWN: {$this->domain->url}")
            ->line("Domain **{$this->domain->url}** is not responding.")
            ->line('Status code: ' . ($this->check->status_code ?? 'No response'))
            ->line('Error: ' . ($this->check->error ?? '—'));
    }
}