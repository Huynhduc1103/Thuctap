<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Logs;
use App\Models\Template;
use App\Models\User;
use Exception;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
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

    public function testmail()
    {
        $name = 'Tạ Huỳnh Đức';
        Mail::send('email.mail', compact('name'), function ($email) use ($name) {
            $email->subject('Thư chúc mừng');
            $email->to('huynhduc110503@gmail.com', $name);
        });
    }

    public function sendall()
    {
        $users = User::all();
        foreach ($users as $user) {
            $name = $user->fullname;
            try {
                Mail::send('email.birthday', compact('name'), function ($email) use ($name, $user) {
                    $email->subject('Thư chúc mừng');
                    $email->to($user->email, $name);
                });
                Logs::create([
                    'user_id' => $user->id,
                    'senddate' => Carbon::now()->format('Y-m-d'),
                    'status' => 'Success',
                    'message_id' => 2,
                    'event_id' => null
                ]);
            } catch (Swift_TransportException $e) {
                Logs::create([
                    'user_id' => $user->id,
                    'senddate' => Carbon::now()->format('Y-m-d'),
                    'status' => 'Error',
                    'message_id' => 2,
                    'event_id' => null
                ]);
                // Xử lý ngoại lệ khi không thể gửi email
                echo response()->json(['message' => 'Không thể gửi email']);
            }
        }
    }

    public function sendlistup(Request $request)
    {
        $list = $request->input('user_id');
        $event = Event::find($request->input('event_id'));
        $eventname = $event->eventname;
        $date = Carbon::parse($event->eventdate);
        $eventdate = $date->format('d-m-Y');
        $listuser = explode(',', $list);
        for ($i = 0; $i < count($listuser); $i++) {
            $user = User::find($listuser[$i]);
            $name = $user->fullname;
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
                    'event_id' => $event->id
                ]);
            } catch (Exception $e) {
                Logs::create([
                    'user_id' => $user->id,
                    'senddate' => Carbon::now()->format('Y-m-d'),
                    'status' => 'Error',
                    'message_id' => 3,
                    'event_id' => $event->id
                ]);
                // Xử lý ngoại lệ khi không thể gửi email
                echo response()->json(['message' => 'Không thể gửi email']);
            }
        }
    }
}
