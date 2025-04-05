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

class ShareVoucher extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        protected Booking $booking,
        protected UploadedFile $voucher
    )
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Voucher Atravel',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.atravel.bookings.voucher',
            with: [
                'client_name' => $this->booking->client_name,
                'company' => Company::NAME,
                'email' => Company::EMAIL,
                'phone' => Company::PHONE
            ]
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->voucher->getRealPath())
                ->as('voucher.pdf')
                ->withMime($this->voucher->getMimeType()),
        ];
    }
}
