<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Carbon;

class EventController extends Controller
{
    public function read()
    {
        $event = Event::all();
        return response()->json($event);
    }

    public function findId(Request $request)
    {
        $id = $request->input('id');
        $event = Event::find($id);
        return response()->json($event);
    }

    public function update($id, Request $request)
    {
        $event = Event::find($id);
        $event->update([
            'eventname' => $request->input('eventname'),
            'eventdate' => $request->input('eventdate')
        ]);
        return response()->json($event);
    }

    public function create(Request $request)
    {
        $event = Event::create([
            'eventname' => $request->input('eventname'),
            'eventdate' => $request->input('eventdate')
        ]);
        return response()->json($event);
    }

    public function delete($id)
    {
        $event = Event::find($id);
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
