<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Mime\Email;

class NewsletterMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array<string, string>  $strings
     */
    public function __construct(
        public string $htmlBody,
        public string $edition,
        public string $unsubscribeUrl,
        public array $strings,
        public string $lang = 'pt_br',
    ) {
        $this->locale($lang);

        // Cabeçalhos RFC 8058 para cancelamento de inscrição em 1 clique (Gmail/Outlook)
        $this->withSymfonyMessage(function (Email $message) {
            $message->getHeaders()
                ->addTextHeader('List-Unsubscribe', "<{$this->unsubscribeUrl}>")
                ->addTextHeader('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click');
        });
    }

    public function envelope(): Envelope
    {
        $subject = strtr($this->strings['mail_newsletter_subject'] ?? 'TechPulse #{edition} — O Pulsar da Tecnologia', [
            '{edition}' => $this->edition,
        ]);

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.newsletter',
            with: [
                'htmlBody' => $this->htmlBody,
                'edition' => $this->edition,
                'unsubscribeUrl' => $this->unsubscribeUrl,
                'strings' => $this->strings,
            ],
        );
    }
}
