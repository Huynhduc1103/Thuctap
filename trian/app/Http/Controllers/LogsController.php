<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Logs;

class LogsController extends Controller
{
    public function read()
    {
        $logs = Logs::all();
        return response()->json($logs);
    }

    public function findId(Request $request)
    {
        $id = $request->input('id');
        $log = Logs::find($id);
        return response()->json($log);
    }

    public function update($id, Request $request)
    {
        $log = Logs::find($id);
        $log->update([
            'user_id' => $request->input('user_id'),
            'senddate' => $request->input('senddate'),
            'event_id' => $request->input('event_id'),
        ]);
        return response()->json($log);
    }

    public function create(Request $request)
    {
        $log = Logs::create([
            'user_id' => $request->input('user_id'),
            'senddate' => $request->input('senddate'),
            'event_id' => $request->input('event_id'),
        ]);
        return response()->json($log);
    }

    public function delete($id)
    {
        $log = Logs::find($id);
        try {
            $log->delete();
            return response()->json(['message' => 'Log ' . $id . ' đã được xóa thành công']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Lỗi xóa log: ' . $e->getMessage()], 500);
        }
    }
}
