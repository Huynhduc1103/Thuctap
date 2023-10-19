<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;

class MessageController extends Controller
{   
    public function readMessage() {
        $mess = Message::all();
        return response()->json($mess);
    }
    public function createMessage(Request $request)
    {
        $messages = Message::create([
            'messagetype' => $request->input('messagetype'),
            'type' => $request->input('type'),
            'event_id' => $request->input('event_id')
        ]);
        return response()->json($messages);
    }

    public function updateMessage(Request $request, $id) {
        $mess = Message::find($id);
        $data = $request->only(['messagetype', 'type', 'event_id']);
        if (!$mess) {
            return response()->json(['error' => 'Message không tồn tại.']);
        }
      
        if (empty(array_filter($data))) {
            return response()->json(['error' => 'Không có dữ liệu để cập nhật.']);
        }
        $mess->update($data);
        return response()->json(['success' => 'Message đã được cập nhật.']);
    }
    public function deleteMessage($id)
    {
        $mess = Message::find($id);     
        if (!$mess) {
            return response()->json(['error' => 'Message không tồn tại.']);
        } else {
            $mess->delete();
            return response()->json(['success' => 'Message đã bị xóa.']);
        }
    }
}
