<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ServicioNotificacion extends Mailable
{
    public function __construct(
        public string $asunto,
        public string $mensaje,
    ) {
    }

    public function envelope(): Envelope
    {
        // El remitente es una cuenta not-reply: las respuestas van al buzón de la empresa.
        return new Envelope(
            replyTo: [new Address(config('euroship.email'), config('euroship.nombre'))],
            subject: $this->asunto,
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.servicio-notificacion');
    }
}
