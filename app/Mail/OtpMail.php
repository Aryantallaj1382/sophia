<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $code;


    public function __construct($code)
    {
        $this->code = $code['code'] ?? $code; // اگر آرایه فرستادی
    }

    public function build()
    {
        return $this->subject('Your Verification Code')
            ->view('email.otp'); // view ایمیل
    }
}
