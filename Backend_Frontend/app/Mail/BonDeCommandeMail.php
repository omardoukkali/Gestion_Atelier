<?php

namespace App\Mail;

use App\Models\Commande;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class BonDeCommandeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Commande $commande) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Bon de Commande #{$this->commande->id} — Atelier",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.bon_commande',   // le corps de l'email (Blade)
        );
    }

    // On attache le PDF généré à la volée
    public function attachments(): array
    {
        $pdf = Pdf::loadView('pdf.bon_commande', ['commande' => $this->commande]);

        return [
            \Illuminate\Mail\Mailables\Attachment::fromData(
                fn () => $pdf->output(),
                "bon_commande_{$this->commande->id}.pdf"
            )->withMime('application/pdf'),
        ];
    }
}
