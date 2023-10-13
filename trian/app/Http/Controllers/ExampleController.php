<?php

namespace App\Http\Controllers;

use App\Models\Logs;
use App\Models\Template;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
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
        $mail = '';
        foreach ($users as $user) {
            $mail = $mail . $user->email . ',';
        }
        $template = Template::create([
            'notification' => 'Email',
            'timer' => Carbon::now()->format('Y-m-d H:i:s'),
            'type' => 'GROUP',
            'data' => $mail,
            'message_id' => 1
        ]);
        //cat chuoi 
        $mang = explode(",", $template->data);
        for ($i = 0; $i < count($mang) - 1; $i++) {
            $user = User::where('email', 'like', '%' . $mang[$i] . '%')->first();
            $name = $user->fullname;
            try {
                Mail::send('email.birthday', compact('name'), function ($email) use ($name, $user) {
                    $email->subject('Thư chúc mừng');
                    $email->to($user->email, $name);
                });
                //echo $template->id;
                $logs = Logs::create([
                    'user_id' => $user->id,
                    'template_id' => $template->id,
                    'senddate' => '2023-10-04',
                    // Carbon::now()->format('Y-m-d')
                    'status' => 'success'
                ]);
            } catch (Swift_TransportException $e) {
                Logs::create([
                    'user_id' => $user->id,
                    'template_id' => $template->id,
                    'senddate' => '2023-10-04',
                    // Carbon::now()->format('Y-m-d')
                    'status' => 'Error'
                ]);
                // Xử lý ngoại lệ khi không thể gửi email
                echo response()->json(['message' => 'Không thể gửi email']);
            }
        }
    }
}
