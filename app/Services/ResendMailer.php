<?php

namespace App\Services;

use Resend\Resend;

class ResendMailer
{
    protected $resend;

    public function __construct()
    {
        $this->resend = new Resend(env('RESEND_API_KEY'));
    }

    public function send($to, $subject, $html)
    {
        return $this->resend->emails->send([
            'from' => config('mail.from.address'),
            'to' => [$to],
            'subject' => $subject,
            'html' => $html,
        ]);
    }
}
