<?php

namespace App\Modules\Bookings\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\UploadedFile;

use App\Modules\Bookings\Models\Booking;
use App\Modules\Bookings\Models\Company;

class ShareTimetable extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        protected string $conductor,
        protected string $email,
        protected string $date,
        protected UploadedFile $timetable
    )
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Escala de Serviços - '. $this->date,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.atravel.bookings.timetable',
            with: [
                'conductor' => $this->conductor,
                'email' => $this->email,
                'date' => $this->date
            ]
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->timetable->getRealPath())
                ->as('timetable.pdf')
                ->withMime($this->timetable->getMimeType()),
        ];
    }
}
