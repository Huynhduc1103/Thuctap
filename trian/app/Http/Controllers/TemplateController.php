<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Template;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TemplateController extends Controller
{
    public function read()
    {
        $template = Template::all();
        return response()->json($template);
    }


    public function create(Request $request)
    {
        // check null
        $validator = Validator::make(
            $request->all(),
            [
                'data' => 'required',
                'event_id' => 'required|exists:events,id',
                'timer' => 'required|date_format:Y-m-d H:i'
            ],
            [
                'required' => 'Trường :attribute không được để trống.',
                'email' => 'Trường :attribute phải là địa chỉ email hợp lệ.',
                'unique' => 'Trường :attribute đã tồn tại.',
                'exists' => 'Trường :attribute không hợp lệ.',
                'date_format' => 'Trường :attribute không đúng định dạng Y-m-d H:i'
            ]
        );
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        $list = $request->input('data');
        $event_id = $request->input('event_id');
        $timer = $request->input('timer'); // Không được lấy ngày giờ hiện tại nghen
        // bắt lỗi sự kiện
        $currentTime = Carbon::now();
        $inputTime = Carbon::createFromFormat('Y-m-d H:i', $timer);
        if ($inputTime->diffInMinutes($currentTime) <= 30 || $inputTime->isPast()) {
            return response()->json(['error' => 'Thời gian phải sau 30 phút so với thời gian hiện tại.'], 422);
        }
        $event = Event::find($request->event_id);
        if ($event->eventdate <= Carbon::now()) {
            return response()->json(['error' => 'Sự kiện đã hoặc đang diễn.'], 422);
        }
        // Tự động xác định giá trị cho trường "type" dựa trên dữ liệu
        $dataArray = explode(',', $list);
        $users = User::WhereIn('id', $dataArray)->get();
        $totalItems = count($users);
        $counter = 0;
        $data = "";
        foreach ($users as $user) {
            $data = $data . $user->id;
            if (++$counter < $totalItems) {
                $data = $data . ',';
            }
        }
        if ($totalItems == 1) {
            $type = 'USER';
        } elseif ($totalItems >= 2) {
            $type = 'GROUP';
        }
        // Tiến hành thêm dữ liệu
        $template = Template::create([
            'timer' => $timer,
            'type' => $type,
            'data' => $data,
            'event_id' => $event_id,
        ]);
        return response()->json($template);
    }


    public function update(Request $request)
    {
        $template = Template::find($request->input('id'));
        if (empty($template)) {
            return response()->json(['error' => 'Template không tồn tại.'], 404);
        }
        // check null
        $validator = Validator::make(
            $request->all(),
            [
                'data' => 'required',
                'event_id' => 'required|exists:events,id',
                'timer' => 'required|date_format:Y-m-d H:i'
            ],
            [
                'required' => 'Trường :attribute không được để trống.',
                'unique' => 'Trường :attribute đã tồn tại.',
                'exists' => 'Trường :attribute không hợp lệ.',
                'date_format' => 'Trường :attribute không đúng định dạng Y-m-d'
            ]
        );
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        $timer = $request->input('timer'); // Không được lấy ngày giờ hiện tại nghen
        $list = $request->input('data');
        $event_id = $request->input('event_id');
        $currentTime = Carbon::now();
        $inputTime = Carbon::createFromFormat('Y-m-d H:i', $timer);
        if ($inputTime->diffInMinutes($currentTime) <= 30 || $inputTime->isPast()) {
            return response()->json(['error' => 'Thời gian phải sau 30 phút so với thời gian hiện tại.'], 422);
        }
        $dataArray = explode(',', $list);
        $users = User::WhereIn('id', $dataArray)->get();
        $totalItems = count($users);
        $counter = 0;
        $data = "";
        foreach ($users as $user) {
            $data = $data . $user->id;
            if (++$counter < $totalItems) {
                $data = $data . ',';
            }
        }
        if ($totalItems == 1) {
            $type = 'USER';
        } elseif ($totalItems >= 2) {
            $type = 'GROUP';
        }
        $template->update([
            'timer' => $timer,
            'data' => $data,
            'type' => $type,
            'event_id' => $event_id
        ]);
        return response()->json($template);
    }


    public function findId(Request $request)
    {
        $template = Template::find($request->input('id'));
        if (empty($template)) {
            return response()->json(['error' => 'Template không tồn tại.'], 404);
        }
        return response()->json($template);
    }

    public function delete(Request $request)
    {
        $template = Template::find($request->input('id'));
        if (empty($template)) {
            return response()->json(['error' => 'Template không tồn tại.'], 404);
        }
        try {
            // Xóa 
            $template->delete();
            // Xóa thành công
            return response()->json(['message' => 'Template ' . $template->id . ' đã được xóa thành công']);
        } catch (\Exception $e) {
            // Xử lý lỗi nếu có
            return response()->json(['message' => 'Lỗi xóa template: ' . $e->getMessage()], 500);
        }
    }
}
