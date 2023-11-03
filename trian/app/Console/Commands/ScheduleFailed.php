<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\Failed;
use App\Models\Logs;
use App\Models\User;
use App\Providers\send;
use Carbon\Carbon;
use Dotenv\Dotenv;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class ScheduleFailed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'failed:send';

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
    public function email($user, $event)
    {
        $name = $user->fullname;
        $eventname = $event->eventname;
        $date = Carbon::parse($event->eventdate);
        $eventdate = $date->format('d-m-Y');
        try {
            Mail::send('email.event', compact('name', 'eventdate', 'eventname'), function ($email) use ($name, $user, $eventdate, $eventname) {
                $email->subject('SỰ KIỆN TRI ÂN KHÁCH HÀNG');
                $email->to($user->email, $name, $eventdate);
            });
            $logs = Logs::where('user_id', $user->id)
            ->where('event_id', $event->id)
            ->first();
            if (empty($logs)) {
                Logs::create([
                    'user_id' => $user->id,
                    'senddate' => Carbon::now()->format('Y-m-d'),
                    'event_id' => $event->id,
                    'sent' => 'EMAIL'
                ]);
            } else {
                $logs->update([
                    'sent' => 'EMAIL - '.$logs->sent
                ]);
            }
            
        } catch (Exception $e) {
            Failed::create([
                'user_id' => $user->id,
                'date' => Carbon::now()->format('Y-m-d'),
                'event_id' => $event->id,
                'type' => 'EMAIL',
                'error'=> $e->getMessage()
            ]);
            // Xử lý ngoại lệ khi không thể gửi email
            echo response()->json(['message' => 'Không thể gửi email']);
        }
    }

    public function sms($user, $event)
    {
        $dotenv = Dotenv::createImmutable(base_path()); // Use the appropriate path to your .env file
        $dotenv->load();
        $APIKey = env('API_KEY');
        $SecretKey = env('SECRET_KEY');
        $BrandName = "Baotrixemay";
        // send sms
        $Content = 'Chuc mung sinh nhat ' . $user->fullname . '. Kinh chuc QK co nhieu suc khoe, thanh cong va hanh phuc! Nhan dip sinh nhat xin gui den ' . $user->fullname . ' coupon {P2,20}. Tran trong.';
        $YourPhone = $user->phone;
        $SendContent = urlencode($Content);
        $data = "http://rest.esms.vn/MainService.svc/json/SendMultipleMessage_V4_get?Phone=$YourPhone&ApiKey=$APIKey&SecretKey=$SecretKey&Content=$SendContent&Brandname=$BrandName&SmsType=2";

        $curl = curl_init($data);
        curl_setopt($curl, CURLOPT_FAILONERROR, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($curl);

        if ($result === false) {
            die(curl_error($curl)); // Display cURL error if there is one
        }
        $obj = json_decode($result, true);
        if ($obj === null) {
            die('Error decoding JSON: ' . json_last_error_msg()); // Display JSON parsing error if there is one
        }
        if ($obj['CodeResult'] == 100) {
            $logs = Logs::where('user_id', $user->id)
            ->where('event_id', $event->id)
            ->first();
            if (empty($logs)) {
                Logs::create([
                    'user_id' => $user->id,
                    'senddate' => Carbon::now()->format('Y-m-d'),
                    'event_id' => $event->id,
                    'sent' => 'SMS'
                ]);
            } else {
                $logs->update([
                    'sent' => $logs->sent . ' - SMS'
                ]);
            }
            
            echo "Gửi thành công";
        } else {
            Failed::create([
                'user_id' => $user->id,
                'date' => Carbon::now()->format('Y-m-d'),
                'event_id' => $event->id,
                'type' => 'SMS',
                'error' => $obj['ErrorMessage']
            ]);
            echo "ErrorMessage: " . $obj['ErrorMessage'];
        }
    }
    public function handle()
    {
        $faileds = Failed::all();
        foreach ($faileds as $failed) {
            $user = User::find($failed->user_id);
            $event = Event::find($failed->event_id);

            if ($failed->type === 'EMAIL') {
                ScheduleFailed::email($user, $event);
                $failed->delete();
            } else {
                ScheduleFailed::sms($user, $event);
                $failed->delete();
            }
        }
    }
}
