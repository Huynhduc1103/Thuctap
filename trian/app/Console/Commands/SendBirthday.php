<?php

namespace App\Console\Commands;

use App\Models\Failed;
use App\Models\Logs;
use App\Models\User;
use Carbon\Carbon;
use Dotenv\Dotenv;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendBirthday extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'message:birthday';

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
        $today = Carbon::now()->format('m-d');
        $users = User::whereRaw('DATE_FORMAT(birthday, "%m-%d") = ?', [$today])->get();
        foreach ($users as $user) {
            $name = $user->fullname;
            try {
                Mail::send('email.birthday', compact('name'), function ($email) use ($name, $user) {
                    $email->subject('CHÚC MỪNG SINH NHẬT');
                    $email->to($user->email, $name);
                });
                Logs::create([
                    'user_id' => $user->id,
                    'senddate' => Carbon::now()->format('Y-m-d'),
                    'event_id' => null
                ]);
            } catch (Exception $e) {
                Failed::create([
                    'user_id' => $user->id,
                    'date' => Carbon::now()->format('Y-m-d'),
                    'event_id' => null,
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
                    'event_id' => null
                ]);
                echo "Gửi thành công";
            } else {
                Failed::create([
                    'user_id' => $user->id,
                    'date' => Carbon::now()->format('Y-m-d'),
                    'event_id' => null,
                    'type' => 'SMS'
                ]);
                echo "ErrorMessage: " . $obj['ErrorMessage'];
            }
        }
    }
}
