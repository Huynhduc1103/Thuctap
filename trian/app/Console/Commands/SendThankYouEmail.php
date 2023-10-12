<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\ThankYouEmail;

class SendThankYouEmail extends Command
{
    protected $signature = 'email:sendthankyou';
    protected $description = 'Send a thank you email to customers';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $email = 'vunguyenbr2912@gmail.com';
        Mail::to($email)->send(new ThankYouEmail());

        $this->info('Thank you email sent to ' . $email);
    }
}
