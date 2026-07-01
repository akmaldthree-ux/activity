<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[Daily Plan] Informasi Pendaftaran Akun',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.account-rejected',
            with: ['user' => $this->user],
        );
    }
}
