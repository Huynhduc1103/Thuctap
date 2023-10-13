<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class SendEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'User:email';

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
        $today = Carbon::now()->format('m-d');
        $users = User::whereRaw('DATE_FORMAT(birthday, "%m-%d") = ?', [$today])->get();
        foreach ($users as $user) {
            $name = $user->fullname;
            Mail::send('email.birthday', compact('name'), function ($email) use ($name, $user) {
                $email->subject('CHÚC MỪNG SINH NHẬT');
                $email->to($user->email, $name);
            });
        }
    }
}
