<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Logs;
use App\Models\Template;
use App\Models\User;
use App\Models\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Swift_TransportException;

class retrycontroller extends Controller
{
    public function retrybirthday()
    {
        $logs= Logs::where('status', 'like', '%' . 'ERROR' . '%')->get();
        foreach ($logs as $log){
            $user = User::find($log->user_id);
            $name = $user->fullname;
        
            if(!empty($log->event_id )){
                $event = Event::find($log->event_id);
                $eventname = $event->eventname;
                $date = Carbon::parse($event->eventdate);
                $eventdate = $date->format('d-m-Y');
                try {
                    Mail::send('email.event', compact('name', 'eventdate', 'eventname'), function ($email) use ($name, $user, $eventdate, $eventname) {
                        $email->subject('SỰ KIỆN TRI ÂN KHÁCH HÀNG');
                        $email->to($user->email, $name, $eventdate);
                        
                    });
                    $log->update([
                        'senddate' => Carbon::now()->format('Y-m-d'),
                        'status' => 'Success',                       
                    ]);
                   
                } catch (Exception $e) {
                    
                    // Xử lý ngoại lệ khi không thể gửi email
                    echo response()->json(['message' => 'Không thể gửi email']);
                }
           
            }   
             else{
                try {
                    Mail::send('email.birthday', compact('name'), function ($email) use ($name, $user) {
                        $email->subject('Thư chúc mừng');
                        $email->to($user->email, $name);
                    });
                    $log->update([
                        'senddate' => Carbon::now()->format('Y-m-d'),
                        'status' => 'Success',                       
                    ]);
               
                } catch (Swift_TransportException $e) {                               
                     echo response()->json(['message' => 'Không thể gửi email']);               
                }
            
            }
 
    }
      

}
}