<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RestPasswordEmail extends Notification
{
    /**
     * The password reset token.
     *
     * @var string
     */
    public $token;

    /**
     * Create a notification instance.
     */
    public function __construct(#[\SensitiveParameter] string $token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $resetUrl = url('/reset-password?' . http_build_query([
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]));

        $expireMinutes = config('auth.passwords.users.expire', 60);

        return (new MailMessage)
            ->subject('Reset Your Password - Bookmark App')
            ->greeting('Hello ' . ($notifiable->name ?? 'there') . ',')
            ->line('You requested a password reset. Click the button below to set a new password.')
            ->action('Reset Password', $resetUrl)
            ->line('This link will expire in ' . $expireMinutes . ' minutes.')
            ->line('If you did not request a password reset, you can ignore this email.')
            ->salutation('Regards, Bookmark App Team');
    }
}
