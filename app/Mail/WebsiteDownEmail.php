<?php

namespace App\Mail;

use App\Models\Website;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WebsiteDownEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $website;
    public $errorMessage;

    /**
     * Create a new message instance.
     */
    public function __construct(Website $website, $errorMessage)
    {
        $this->website = $website;
        $this->errorMessage = $errorMessage;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "🚨 Website Down: {$this->website->name}"
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.website-down',
            with: [
                'website' => $this->website,
                'errorMessage' => $this->errorMessage,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
