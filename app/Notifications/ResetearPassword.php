<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetearPassword extends ResetPassword
{
    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $resetUrl = $this->resetUrl($notifiable);
        return (new MailMessage)
            ->subject('Restablecer contraseña')
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line('Has solicitado restablecer la contraseña de tu cuenta.')
            ->line('Haz clic en el siguiente botón para establecer una nueva contraseña.')
            ->action('Restablecer contraseña', $resetUrl)
            ->line('Este enlace caducará en 60 minutos.')
            ->line('Si no has solicitado este cambio, puedes ignorar este mensaje.')
            ->salutation('Saludos, ' . config('app.name'));
    }
}
