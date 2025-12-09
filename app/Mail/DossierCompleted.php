<?php

namespace App\Mail;

use App\Models\Dossier;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DossierCompleted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Dossier $dossier
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Votre dossier sécurisé est prêt 🔒',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.dossier_completed',
            with: [
                'url' => route('dossiers.download', [
                    'dossier' => $this->dossier->id, 
                    'token' => $this->dossier->download_token
                ]),
                'expiration' => $this->dossier->expires_at->setTimezone('Europe/Paris')->format('d/m/Y à H:i'),
            ]
        );
    }
}