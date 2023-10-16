<?php

namespace App\Console\Commands;

use App\Models\Message;
use App\Models\User;
use Carbon\Carbon;
use Dotenv\Dotenv;
use Illuminate\Console\Command;

class SmsEvent extends Command
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
        // Load environment variables from .env file
        $dotenv = Dotenv::createImmutable(base_path()); // Use the appropriate path to your .env file
        $dotenv->load();
        $APIKey = env('API_KEY');
        $SecretKey = env('SECRET_KEY');

        $today = Carbon::now()->format('m-d');
        $messages = Message::whereRaw('DATE_FORMAT(eventdate, "%m-%d") = ?', [$today])->get();
        $users = User::all();
        
        foreach ($users as $user) {
            $YourPhone = $user->phone;
            $Content = "Chuc mung sinh nhat {P2,50}. Kinh chuc QK co nhieu suc khoe, thanh cong va hanh phuc! Nhan dip sinh nhat xin gui den {P2,50} coupon {P2,20}. Tran trong.";
            $BrandName = "Baotrixemay";
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
                echo "Gửi thành công";
            } else {
                echo "ErrorMessage: " . $obj['ErrorMessage'];
            }
        }
    }
}
