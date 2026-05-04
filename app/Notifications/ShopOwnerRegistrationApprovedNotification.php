<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ShopOwnerRegistrationApprovedNotification extends Notification
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your shop owner account has been approved')
            ->greeting('Hello '.$notifiable->name.',')
            ->line('Your shop owner registration has been approved by the platform admin.')
            ->line('Your first shop profile has been prepared from the registration details you submitted.')
            ->line('You can now sign in to manage services and orders for your laundry business.')
            ->action('Sign in as Shop Owner', route('admin.login'))
            ->line('If you did not request this account, please contact support.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'status' => 'approved',
        ];
    }
}
