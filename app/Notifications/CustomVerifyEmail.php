<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

class CustomVerifyEmail extends Notification
{
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
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Verify Your Email for Bookmark App')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Thank you for registering! Please verify your email by clicking the button below.')
            ->action('Verify Email', $verificationUrl)
            ->line('If you did not create an account, no further action is required.')
            ->salutation('Regards, Bookmark App Team');
    }

    /**
     * Generate the signed verification URL.
     */
    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'verification.verify', // route name
            Carbon::now()->addMinutes(60), // expires in 60 minutes
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}
