<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Logs;
use App\Models\User;
use Carbon\Carbon;
use Dotenv\Dotenv;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class sendType extends Controller
{
    public function sendType(Request $request){

        $dotenv = Dotenv::createImmutable(base_path()); // Use the appropriate path to your .env file
        $dotenv->load();
        $APIKey = env('API_KEY');
        $SecretKey = env('SECRET_KEY');
        $Content = "Chuc mung sinh nhat {P2,50}. Kinh chuc QK co nhieu suc khoe, thanh cong va hanh phuc! Nhan dip sinh nhat xin gui den {P2,50} coupon {P2,20}. Tran trong.";
        $BrandName = "Baotrixemay";

        $keyword = $request->input('keyword');
        $event_id = $request->input('event_id');
        $event = Event::find($event_id);
        $users = User::where('groupcode', 'like', '%' . $keyword . '%')->get();
        foreach($users as $user){
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
                        'status' => 'Success',
                        'message_id' => 3,
                        'event_id' => $event_id
                    ]);
                } catch (Exception $e) {
                    Logs::create([
                        'user_id' => $user->id,
                        'senddate' => Carbon::now()->format('Y-m-d'),
                        'status' => 'Error',
                        'message_id' => 3,
                        'event_id' => $event_id
                    ]);
                    // Xử lý ngoại lệ khi không thể gửi email
                    echo response()->json(['message' => 'Không thể gửi email']);
                }
                // send sms
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
                        'status' => 'Success',
                        'message_id' => 4,
                        'event_id' => $event_id
                    ]);
                    echo "Gửi thành công";
                } else {
                    Logs::create([
                        'user_id' => $user->id,
                        'senddate' => Carbon::now()->format('Y-m-d'),
                        'status' => 'Error',
                        'message_id' => 4,
                        'event_id' => $event_id
                    ]);
                    echo "ErrorMessage: " . $obj['ErrorMessage'];
                }
        }
    }
}