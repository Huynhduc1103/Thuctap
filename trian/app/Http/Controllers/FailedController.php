<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Failed;
use App\Models\User;
use App\Models\Event;
use Illuminate\Support\Facades\Validator;

class FailedController extends Controller
{
    public function readFailed()
    {
    
        $failed = Failed::all();
        return response()->json($failed);
        if (!$failed) {
            return response()->json(['message' => 'Failed rỗng.']);
        } else {
            return response()->json($failed);
        }
    }

    public function updateFailed($id, Request $request)
    {
        $user = User::find($request->input('user_id'));
        $event = Event::find($request->input('event_id'));
        
        if (!$user) {
            return response()->json(['error' => 'Người dùng không tồn tại.']);
        } 
        if (!$event) {
            return response()->json(['error' => 'Event không tồn tại.']);
        }
        $failed = Failed::find($id);
        if (!$failed) {
            return response()->json(['message' => 'Failed không tồn tại.']);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'date' => 'required',
            'event_id' => 'required',
            'type' => 'required',
            'error' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        $failed->update([
            'user_id' => $request->input('user_id'),
            'date' => $request->input('date'),
            'event_id' => $request->input('event_id'),
            'type' => $request->input('type'),
            'error' => $request->input('error')
        ]);
        return response()->json($failed);
    }

    public function createFailed(Request $request)
    {
        $user = User::find($request->input('user_id'));
        $event = Event::find($request->input('event_id'));
        if (!$user){
            return response()->json(['error' => 'Người dùng không tồn tại.']);
        }
        if (!$event)    {
            return response()->json(['error' => 'Event không tồn tại.']);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'date' => 'required|date_format:Y-m-d',
            'event_id' => 'required',
            'type' => 'required',
            'error' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $failed = Failed::create([
            'user_id' => $request->input('user_id'),
            'date' => $request->input('date'),
            'event_id' => $request->input('event_id'),
            'type' => $request->input('type'),
            'error' => $request->input('error')
        ]);
        return response()->json($failed);
    }

    public function deleteFailed($id)
    {
        $failed = Failed::find($id);
        if (!$failed) {
            return response()->json(['message' => 'Failed không tồn tại.']);
        }
        try {
            // Xóa
            $failed->delete();
            // Xóa thành công
            return response()->json(['message' => 'Failed ' . $id . ' đã được xóa thành công']);
        } catch (Exception $e) {
            // Xử lý lỗi nếu có
            return response()->json(['message' => 'Lỗi xóa Failed: ' . $e->getMessage()], 500);
        }
    }    
}
