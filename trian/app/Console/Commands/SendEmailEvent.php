<?php

namespace App\Console\Commands;

use App\Models\Message;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendEmailEvent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'message:sendevent';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $today = Carbon::now()->format('Y-m-d');
        $event = Message::whereRaw('DATE_FORMAT(eventdate, "%Y-%m-%d") = ?', [$today])->get();
        $users = User::all();
        foreach ($users as $user) {
            $name = $user->fullname;
            $eventdate = Carbon::now()->format('d-m-Y');
            Mail::send('email.event', compact('name', 'eventdate'), function ($email) use ($name, $user, $eventdate) {
                $email->subject('SỰ KIỆN TRI ÂN KHÁCH HÀNG');
                $email->to($user->email, $name, $eventdate);
            });
        }
    }
}
