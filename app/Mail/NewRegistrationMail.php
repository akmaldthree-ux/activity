<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewRegistrationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $manager, public User $applicant) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[Daily Plan] Pendaftar Baru Menunggu Persetujuan',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.new-registration',
            with: ['manager' => $this->manager, 'applicant' => $this->applicant],
        );
    }
}
