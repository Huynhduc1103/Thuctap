<?php

namespace App\Http\Controllers;

use App\Models\Failed;
use Dotenv\Dotenv;
use Illuminate\Http\Request;
use App\Models\Logs;
use App\Models\Template;
use App\Models\User;
use App\Models\Event;
use Exception;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Swift_TransportException;

class retrycontroller extends Controller
{
    public function retrybirthday()
    {
        $dotenv = Dotenv::createImmutable(base_path()); // Use the appropriate path to your .env file
        $dotenv->load();
        $APIKey = env('API_KEY');
        $SecretKey = env('SECRET_KEY');
        $BrandName = "Baotrixemay";
        $faileds = Failed::all();
        foreach ($faileds as $failed) {
            $user = User::find($failed->user_id);
            $name = $user->fullname;

            if (!empty($log->event_id)) {
                if ($failed->type==="EMAIL"){

                
                $event = Event::find($failed->event_id);
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
                        'event_id' => $event->id
                        
                    ]);
                    $failed->delete();
                } catch (Exception $e) {

                    // Xử lý ngoại lệ khi không thể gửi email
                    echo response()->json(['message' => 'Không thể gửi email']);
                }
            }else{
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
                        'event_id' => $event->id
                        
                    ]);
                    $failed->delete();
                    echo "Gửi thành công";
                } 
            }
        } else {
            if ($failed->type==="EMAIL"){
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
            }else{
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
                        'event_id' => $event->id
                        
                    ]);
                    $failed->delete();
                    echo "Gửi thành công";
                } 
            }
            }
        }
    }
}
