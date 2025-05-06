<?php

// app/Notifications/CustomVerifyEmail.php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class CustomVerifyEmail extends Notification
{
    protected $verificationCode;

    public function __construct($verificationCode)
    {
        $this->verificationCode = $verificationCode;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->line('Your verification code is ' . $this->verificationCode)
            ->action('Verify Email', url('api/verify-email/' . $this->verificationCode))
            ->line('Thank you for registering!');
    }
}
