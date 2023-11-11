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

    public function findId(Request $request)
    {
        $event = Event::find($request->input('id'));
        if (empty($event)) {
            return response()->json(['error' => 'Sự kiện không tồn tại.'], 404);
        }
        return response()->json($event);
    }

    public function update(Request $request)
    {
        $event = Event::find($request->input('id'));
        if (empty($event)) {
            return response()->json(['error' => 'Sự kiện không tồn tại.'], 404);
        }
        // bỏ trống
        $eventIdToUpdate = $request->input('event_id');

        $eventIdToUpdate = $request->input('event_id');

        $validator = Validator::make(
            $request->all(),
            [
                'eventname' => 'required|unique:events,eventname,' . $eventIdToUpdate . ',id,eventdate,' . $request->input('eventdate'),
                'eventdate' => 'required|date_format:Y-m-d|after:' . Carbon::now()->format('Y-m-d'),
            ],
            [
                'eventname.required' => 'Tên sự kiện không được để trống.',
                'eventname.unique' => 'Sự kiện này đã tồn tại trong cơ sở dữ liệu cho ngày đã chọn.',
                'eventdate.required' => 'Ngày diễn ra sự kiện không được để trống.',
                'eventdate.after' => 'Ngày diễn ra sự kiện phải lớn hơn ngày hiện tại.',
                'eventdate.date_format' => 'Ngày diễn ra sự kiện không đúng định dạng Y-m-d.',
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
        return response()->json($event);
    }


    public function create(Request $request)
    {
        // bỏ trống
        $validator = Validator::make(
            $request->all(),
            [
                'eventname' => 'required|unique:events,eventname,NULL,id,eventdate,' . $request->input('eventdate'),
                'eventdate' => 'required|date_format:Y-m-d|after:' . Carbon::now()->format('Y-m-d'),
            ],
            [
                'eventname.required' => 'Tên sự kiện không được để trống.',
                'eventname.unique' => 'Sự kiện này đã tồn tại trong cơ sở dữ liệu cho ngày đã chọn.',
                'eventdate.required' => 'Ngày diễn ra sự kiện không được để trống.',
                'eventdate.after' => 'Ngày diễn ra sự kiện phải lớn hơn ngày hiện tại.',
                'eventdate.date_format' => 'Ngày diễn ra sự kiện không đúng định dạng Y-m-d.',
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

    public function delete(Request $request)
    {
        $event = Event::find($request->input('id'));
        if (empty($event)) {
            return response()->json(['error' => 'Sự kiện không tồn tại.'], 404);
        }
        try {
            // Xóa 
            $event->delete();
            // Xóa thành công
            return response()->json(['message' => 'Sự kiện ' . $event->eventname . ' đã được xóa thành công.']);
        } catch (\Exception $e) {
            // Xử lý lỗi nếu có
            return response()->json(['message' => 'Lỗi xóa sự kiện: ' . $e->getMessage()], 500);
        }
    }
}
