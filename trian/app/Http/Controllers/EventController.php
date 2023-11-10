<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    public function read()
    {
        $events = Event::all();
        //bo trong
        if ($events === null) {
            return response()->json(['message' => 'Không có dữ liệu nào nào được tìm thấy.'], 404);
        }
    
        return response()->json($events);
    }

    public function findId($id)
    {
        
        $event = Event::find($id);
        if(empty($event)) {
            return response()->json(['error'=> 'Event không tồn tại.'],404);
        }
        return response()->json($event);
    }

    public function update($id, Request $request)
    {
        $event = Event::find($id);
        if(!$event){
            return response()->json(['message' => 'Không tồn tại.'], 404);
        }
        // bỏ trống
        $validator = Validator::make($request->all(), [
            'eventname' => 'required',
            'eventdate' => 'required|date_format:Y-m-d|after_or_equal:'.Carbon::now()->format('Y-m-d'),
        ],
        [

            'required' => 'Trường :attribute không được để trống.',
            'after_or_equal' => 'Trường :attribute không được nhỏ hơn ngày hiện tại',
            'date_format' => 'Trường :attribute phải là ngày không để hợp lệ Y-m-d.',
            
            // Thêm các thông báo lỗi tùy chỉnh cho các quy tắc validation khác nếu cần
        ]
    
    );
        
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        $eventname = $request->input('eventname');
        $eventdate = $request->input('eventdate');
       
        $event->update([
            'eventname' => $eventname,
                'eventdate' => $eventdate,
        ]);
        return response()->json($event);}
    

    public function create(Request $request)
    {
        // bỏ trống
        $validator = Validator::make($request->all(), [
            'eventname' => 'required',
            'eventdate' => 'required|date_format:Y-m-d|after_or_equal:'.Carbon::now()->format('Y-m-d'),
        ],
        [
            'required' => 'Trường :attribute không được để trống.',
            'after_or_equal' => 'Trường :attribute không được nhỏ hơn ngày hiện tại',
            'date_format' => 'Trường :attribute phải là ngày không để hợp lệ Y-m-d.',
            
            // Thêm các thông báo lỗi tùy chỉnh cho các quy tắc validation khác nếu cần
        ]
    
    );
        
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        $eventname = $request->input('eventname');
        $eventdate = $request->input('eventdate');
        
        
            $events = Event::create([
                'eventname' => $eventname,
                'eventdate' => $eventdate,
            ]);
            return response()->json($events);
        
        
        
    }

    public function delete($id)
    {   
        $event = Event::find($id);
        if(!$event){
            return response()->json(['message' => 'Không tồn tại.'], 404);
        }
            
        try {
            // Xóa 
            $event->delete();
            // Xóa thành công
            return response()->json(['message' => 'event ' . $id . ' đã được xóa thành công']);
        } catch (\Exception $e) {
            // Xử lý lỗi nếu có
            return response()->json(['message' => 'Lỗi xóa event: ' . $e->getMessage()], 500);
        }
        
        
    }
}
