<?php

namespace App\Mail;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReportReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user, public Carbon $date) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[Daily Plan] Pengingat: Isi Report Sore Hari Ini',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.report-reminder',
            with: ['user' => $this->user, 'date' => $this->date],
        );
    }
}
