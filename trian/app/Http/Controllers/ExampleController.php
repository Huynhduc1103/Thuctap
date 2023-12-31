<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Failed;
use App\Models\Logs;
use App\Models\Template;
use App\Models\User;
use Dotenv\Dotenv;
use Exception;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Swift_TransportException;

class ExampleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
                    'sent' => 'EMAIL - ' . $logs->sent
                ]);
            }
        } catch (Exception $e) {
            Failed::create([
                'user_id' => $user->id,
                'date' => Carbon::now()->format('Y-m-d'),
                'event_id' => $event->id,
                'type' => 'EMAIL',
                'error' => $e->getMessage()
            ]);
            // Xử lý ngoại lệ khi không thể gửi email
            echo response()->json(['message' => 'Không thể gửi email']);
        }
    }

    public function sendall(Request $request)
    {
        $event = Event::find($request->input('id'));
        $users = User::all();
        foreach ($users as $user) {
            $logs = Logs::where('user_id', $user->id)
                ->where('event_id', $event->id)
                ->first();
            if (empty($logs)) {
                ExampleController::email($user, $event);
                ExampleController::sms($user, $event);
            }
        }
    }

    public function sendlistup(Request $request)
    {
        // check null
        $validator = Validator::make(
            $request->all(),
            [
                'user_id' => 'required',
                'event_id' => 'required|exists:events,id'
            ],
            [
                'required' => 'Trường :attribute không được để trống.',
                'exists' => 'Trường :attribute không hợp lệ.'
            ]
        );
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        $list = $request->input('user_id');
        $event = Event::find($request->input('event_id'));

        $listuser = explode(',', $list);
        $users = User::WhereIn('id', $listuser)->get();
        foreach ($users as $user) {
            ExampleController::email($user, $event);
            ExampleController::sms($user, $event);
        }
    }

    public function sendNow(Request $request) {
         // check null
        $validator = Validator::make(
            $request->all(),
            [
                'user_id' => 'required',
                'event_id' => 'required|exists:events,id'
            ],
            [
                'required' => 'Trường :attribute không được để trống.',
                'exists' => 'Trường :attribute không hợp lệ.'
            ]
        );
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        $event = Event::find($request->input('event_id'));
        $list = explode(',', $request->input('user_id'));
        $listusers = [];
        for ($i = 0; $i < count($list); $i++) {
            $user = User::find($list[$i]);
            if(empty($user)){
                return response()->json(['error'=> 'Có User không tồn tại trong hệ thống!'],404);
            } else {
                array_push($listusers, $user->id);
            }
        }
        $users = User::WhereIn('id', $listusers)->get();
        foreach( $users as $user) {
            ExampleController::email($user, $event);
            ExampleController::sms($user, $event);
        }
    }
}
