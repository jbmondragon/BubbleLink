<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ShopOwnerRegistrationRejectedNotification extends Notification
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
            ->subject('Your shop owner registration was not approved')
            ->greeting('Hello '.$notifiable->name.',')
            ->line('Your shop owner registration was reviewed and was not approved at this time.')
            ->line('If you believe this was a mistake, please contact the platform admin for clarification before submitting another request.')
            ->action('Return to BubbleLink', route('customer.shops.index'))
            ->line('Thank you for your interest in BubbleLink.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'status' => 'rejected',
        ];
    }
}
