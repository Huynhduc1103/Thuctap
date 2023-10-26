<?php

namespace App\Http\Controllers;

use App\Models\Logs;
use Illuminate\Http\Request;

class indexcontroller extends Controller
{
    public function read() {
        $logs = Logs::all();
        return response()->json($logs);
    }

    public function findId(Request $request) {
        $id = $request->input('id');
        $logs = Logs::find($id);
        return response()->json($logs);
    }

    public function update($id, Request $request) {
        $logs = Logs::find($id);
        $logs->update([
            'senddate' => $request->input('senddate'),
        ]);
        return response()->json($logs);
    }

    public function create(Request $request) {
        $logs = Logs::create([
            'user_id' => $request->input('user_id'),
            'senddate' => $request->input('senddate'),
            'status_id' => $request->input('status_id')
        ]);
        return response()->json($logs);
    }

    
    public function delete($id) {
        $logs = Logs::find($id);
        try {
            // Xóa user
            $logs->delete();
            // Xóa thành công
            return response()->json(['message' => 'logs ' . $id . ' đã được xóa thành công']);
        } catch (\Exception $e) {
            // Xử lý lỗi nếu có
            return response()->json(['message' => 'Lỗi xóa Logs: ' . $e->getMessage()], 500);
        }
    }

}
