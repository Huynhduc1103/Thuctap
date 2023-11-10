<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Failed;
use App\Models\User;
use App\Models\Event;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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

    public function updateFailed(Request $request)
    {
        $customMessages = [
            'user_id.required' => 'Trường user_id không được để trống.',
            'user_id.exists' => 'User không tồn tại trong hệ thống.',
            'date.required' => 'Trường date không được để trống.',
            'date.date' => 'Trường date phải là một ngày hợp lệ.',
            'date.date_format' => 'Trường date phải đúng định dạng năm tháng ngày.',
            'date.date_equals' => 'Ngày phải bằng với ngày hiện tại ' . Carbon::now()->format('Y-m-d') . '.',
            'event_id.required' => 'Trường event_id không được để trống.',
            'event_id.exists' => 'Event không tồn tại trong hệ thống.',
            'type.required' => 'Trường type không được để trống.',
            'type.in' => 'Trường type chỉ được là EMAIL hoặc SMS.',
            'error.required' => 'Trường error không được để trống.',
        ];

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date_format:Y-m-d|date_equals:' . Carbon::now()->format('Y-m-d'),
            'event_id' => 'required|exists:events,id',
            'type' => 'required|in:EMAIL,SMS',
            'error' => 'required',
        ], $customMessages);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        $failed = Failed::find($request->id);
        if (!$failed) {
            return response()->json(['message' => 'Failed không tồn tại.']);
        }
        $failed->update([
            'user_id' => $request->user_id,
            'date' => $request->date,
            'event_id' => $request->event_id,
            'type' => $request->type,
            'error' => $request->error
        ]);
        return response()->json($failed);
    }

    public function createFailed(Request $request)
    {
        $customMessages = [
            'user_id.required' => 'Trường user_id không được để trống.',
            'user_id.exists' => 'User không tồn tại trong hệ thống.',
            'date.required' => 'Trường date không được để trống.',
            'date.date' => 'Trường date phải là một ngày hợp lệ.',
            'date.same' => 'Ngày phải bằng với ngày hiện tại.',
            'event_id.required' => 'Trường event_id không được để trống.',
            'event_id.exists' => 'Event không tồn tại trong hệ thống.',
            'type.required' => 'Trường type không được để trống.',
            'type.in' => 'Trường type chỉ được là "EMAIL" hoặc "SMS".',
            'error.required' => 'Trường error không được để trống.'
        ];
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date_format:Y-m-d|date_equals:' . Carbon::now()->format('Y-m-d'),
            'event_id' => 'required|exists:events,id',
            'type' => 'required|in:EMAIL,SMS',
            'error' => 'required',
        ], $customMessages);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        $existingRecord = DB::table('faileds')
            ->where('user_id', $request->input('user_id'))
            ->where('event_id', $request->input('event_id'))
            ->where('type', $request->input('type'))
            ->first();

        if ($existingRecord) {
            // Trả về lỗi vì kết hợp đã tồn tại
            return response()->json(['error' => 'Đã tồn tại người dùng, sự kiện, lỗi này.', 422]);
        }

        $failed = Failed::create([
            'user_id' => $request->user_id,
            'date' => $request->date,
            'event_id' => $request->event_id,
            'type' => $request->type,
            'error' => $request->error
        ]);
        return response()->json($failed);
    }

    public function deleteFailed(Request $request)
    {
        $failed = Failed::find($request->id);
        if (!$failed) {
            return response()->json(['message' => 'Failed không tồn tại.']);
        }
        try {
            // Xóa
            $failed->delete();
            // Xóa thành công
            return response()->json(['message' => 'Failed ' . $failed->id . ' đã được xóa thành công']);
        } catch (Exception $e) {
            // Xử lý lỗi nếu có
            return response()->json(['message' => 'Lỗi xóa Failed: ' . $e->getMessage()], 500);
        }
    }

    public function findId(Request $request)
    {
        $id = $request->id;
        $failed = Failed::find($id);
        if (empty($failed)) {
            return response()->json(['message' => 'Không tìm thấy failed.'], 404);
        }
        return response()->json($failed);
    }
}
