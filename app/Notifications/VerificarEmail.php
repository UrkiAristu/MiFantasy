<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

class VerificarEmail extends VerifyEmail
{
    /**
     * Construye el mensaje del correo en español.
     */
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Verifica tu dirección de correo electrónico')
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line('Por favor, haz clic en el siguiente botón para verificar tu dirección de correo electrónico.')
            ->action('Verificar correo electrónico', $verificationUrl)
            ->line('Si no has creado una cuenta, no es necesario realizar ninguna acción.')
            ->salutation('Saludos, ' . config('app.name'));
    }
}
