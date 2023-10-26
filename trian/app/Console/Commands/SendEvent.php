<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\Failed;
use App\Models\Logs;
use App\Models\Template;
use App\Models\User;
use Carbon\Carbon;
use Dotenv\Dotenv;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendEvent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'message:event';

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
        $dotenv = Dotenv::createImmutable(base_path()); // Use the appropriate path to your .env file
        $dotenv->load();
        $APIKey = env('API_KEY');
        $SecretKey = env('SECRET_KEY');
        $BrandName = "Baotrixemay";
        $today = Carbon::now()->format('Y-m-d H-i');
        $timers = Template::whereRaw('DATE_FORMAT(timer, "%Y-%m-%d %H-%i") = ?', [$today])->get();
        foreach ($timers as $timer) {
            $listuser = explode(",", $timer->data);
            $event = Event::find($timer->event_id);
            for ($i = 0; $i < count($listuser); $i++) {
                $user = User::find($listuser[$i]);
                $name = $user->fullname;
                $eventname = $event->eventname;
                $date = Carbon::parse($event->eventdate);
                $eventdate = $date->format('d-m-Y');
                try {
                    Mail::send('email.event', compact('name', 'eventdate', 'eventname'), function ($email) use ($name, $user, $eventdate, $eventname) {
                        $email->subject('SỰ KIỆN TRI ÂN KHÁCH HÀNG');
                        $email->to($user->email, $name, $eventdate);
                    });
                    Logs::create([
                        'user_id' => $user->id,
                        'senddate' => Carbon::now()->format('Y-m-d'),
                        'event_id' => $timer->event_id
                    ]);
                } catch (Exception $e) {
                    Failed::create([
                        'user_id' => $user->id,
                        'date' => Carbon::now()->format('Y-m-d'),
                        'event_id' => $timer->event_id,
                        'type' => 'EMAIL'
                    ]);
                    // Xử lý ngoại lệ khi không thể gửi email
                    echo response()->json(['message' => 'Không thể gửi email']);
                }
                // send sms
                $Content = "Chuc mung sinh nhat" . $name . ". Kinh chuc QK co nhieu suc khoe, thanh cong va hanh phuc! Nhan dip sinh nhat xin gui den " . $name . " coupon 20000. Tran trong.";
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
                    Logs::create([
                        'user_id' => $user->id,
                        'senddate' => Carbon::now()->format('Y-m-d'),
                        'event_id' => $timer->event_id
                    ]);
                    echo "Gửi thành công";
                } else {
                    Failed::create([
                        'user_id' => $user->id,
                        'date' => Carbon::now()->format('Y-m-d'),
                        'event_id' => $timer->event_id,
                        'type' => 'SMS'
                    ]);
                    echo "ErrorMessage: " . $obj['ErrorMessage'];
                }
            }
        }
    }
}
