<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Failed;

class FailedController extends Controller
{
    public function readFailed()
    {
        $failed = Failed::all();
        return response()->json($failed);
    }

    public function updateFailed($id, Request $request)
    {
        $failed = Failed::find($id);
        $failed->update([
            'user_id' => $request->input('user_id'),
            'date' => $request->input('date'),
            'event_id' => $request->input('event_id'),
            'type' => $request->input('type')
        ]);
        return response()->json($failed);
    }

    public function createFailed(Request $request)
    {
        $failed = Failed::create([
            'user_id' => $request->input('user_id'),
            'date' => $request->input('date'),
            'event_id' => $request->input('event_id'),
            'type' => $request->input('type')
        ]);
        return response()->json($failed);
    }

    public function deleteFailed($id)
    {
        $failed = Failed::find($id);
        try {
            // Xóa 
            $failed->delete();
            // Xóa thành công
            return response()->json(['message' => 'Failed ' . $id . ' đã được xóa thành công']);
        } catch (\Exception $e) {
            // Xử lý lỗi nếu có
            return response()->json(['message' => 'Lỗi xóa Failed: ' . $e->getMessage()], 500);
        }
    }
}
