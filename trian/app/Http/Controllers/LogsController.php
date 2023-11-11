<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Logs;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class LogsController extends Controller
{
    public function read()
    {
        $logs = Logs::all();

        if ($logs->isEmpty()) {
            return response()->json(['message' => 'Không tìm thấy logs'], 404);
        } else {
            return response()->json(['logs' => $logs], 200);
        }
    }

    public function findId(Request $request)
    {
        $id = $request->input('id');
        $log = Logs::find($id);
        if (empty($log)) {
            return response()->json(['message' => 'Không tìm thấy Logs.'], 404);
        }
        return response()->json($log, 200);
    }

    public function update(Request $request)
    {
        $log = Logs::find($request->id);
        if (empty($log)) {
            return response()->json(['message' => 'Logs không tồn tại trong hệ thống.'], 404);
        }
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'senddate' => 'required|date_format:Y-m-d|date_equals:' . Carbon::now()->format('Y-m-d'),
            'event_id' => 'required|exists:events,id',
            'sent' => 'required',
        ], [
            'user_id.required' => 'Trường user_id là bắt buộc.',
            'user_id.exists' => 'Giá trị của user_id không tồn tại trong cơ sở dữ liệu.',
            'senddate.required' => 'Trường senddate là bắt buộc.',
            'senddate.date_format' => 'Định dạng của trường senddate phải là Y-m-d.',
            'senddate.date_equals' => 'Trường senddate phải bằng ngày hiện tại.',
            'event_id.required' => 'Trường event_id là bắt buộc.',
            'event_id.exists' => 'Giá trị của event_id không tồn tại trong cơ sở dữ liệu.',
            'sent.required' => 'Trường sent là bắt buộc.',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        $existingRecord = DB::table('logs')
            ->where('user_id', $request->input('user_id'))
            ->where('event_id', $request->input('event_id'))
            ->first();

        if ($existingRecord) {
            // Trả về lỗi vì kết hợp đã tồn tại
            return response()->json(['error' => 'Đã tồn tại người dùng, sự kiện này.', 422]);
        }
        $log->update([
            'user_id' => $request->input('user_id'),
            'senddate' => $request->input('senddate'),
            'event_id' => $request->input('event_id'),
            'sent' => $request->input('sent'),
        ]);
        return response()->json($log, 200);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id|unique:logs,user_id,NULL,id,event_id,' . $request->input('event_id'),
            'senddate' => 'required|date_format:Y-m-d|date_equals:' . Carbon::now()->format('Y-m-d'),
            'event_id' => 'required|exists:events,id|unique:logs,event_id,NULL,id,user_id,' . $request->input('user_id'),
            'sent' => 'required',
        ], [
            'user_id.required' => 'Trường user_id là bắt buộc.',
            'user_id.exists' => 'Giá trị của user_id không tồn tại trong cơ sở dữ liệu.',
            'user_id.unique' => 'Giá trị của user_id và event_id đã tồn tại trong bảng logs.',
            'senddate.required' => 'Trường senddate là bắt buộc.',
            'senddate.date_format' => 'Định dạng của trường senddate phải là Y-m-d.',
            'senddate.date_equals' => 'Trường senddate phải bằng ngày hiện tại.',
            'event_id.required' => 'Trường event_id là bắt buộc.',
            'event_id.exists' => 'Giá trị của event_id không tồn tại trong cơ sở dữ liệu.',
            'event_id.unique' => 'Giá trị của user_id và event_id đã tồn tại trong bảng logs.',
            'sent.required' => 'Trường sent là bắt buộc.',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        $log = Logs::create([
            'user_id' => $request->input('user_id'),
            'senddate' => $request->input('senddate'),
            'event_id' => $request->input('event_id'),
            'sent' => $request->input('sent'),
        ]);
        return response()->json($log, 200);
    }

    public function delete(Request $request)
    {
        $log = Logs::find($request->id);
        if (empty($log)) {
            return response()->json(['message' => 'Không tồn tại Logs trong hệ thống.'], 404);
        }
        $log->delete();
        return response()->json(['message' => 'Đã xóa Logs có id ' . $log->id . '.'], 200);
    }
}
