<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Logs;
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
        try {
            $id = $request->input('id');
            $log = Logs::find($id);

            if ($log) {
                return response()->json($log, 200);
            } else {
                return response()->json(['message' => 'Không tìm thấy log ' . $id], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Có lỗi xảy ra: ' . $e->getMessage()], 500);
        }
    }

    public function update( Request $request)
    {
        try {
            $id = $request->input('id');
            $log = Logs::find($id);
    
            if ($log) {
                $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'senddate' => 'required|date_format:Y-m-d',
                    'event_id' => ['required', Rule::exists('events', 'id')],
                    'sent' => 'required',
                ], [
                    'user_id.required' => 'Vui lòng nhập userId',
                    'senddate.date_format' => 'Định dạng ngày không đúng.',
                    'event_id.exists' => 'Event ID không tồn tại.',
                    'required' => 'Vui lòng nhập đầy đủ thông tin',
                    'senddate.required' => 'Vui lòng nhập ngày gửi',
                    'event_id.required' => 'Vui lòng nhập id của event',
                    'sent.required' => 'Vui lòng nhập cách gửi'
                ]);
                
                if ($validator->fails()) {
                    $errors = $validator->errors();
                    $errorMessages = [];
                
                    if ($errors->has('user_id')) {
                        $errorMessages['user_id'] = $errors->first('user_id');
                    }
                
                    if ($errors->has('senddate')) {
                        $errorMessages['senddate'] = $errors->first('senddate');
                    }
                
                    if ($errors->has('event_id')) {
                        $errorMessages['event_id'] = $errors->first('event_id');
                    }
                
                    if ($errors->has('sent')) {
                        $errorMessages['sent'] = $errors->first('sent');
                    }
                
                    return response()->json(['message' => $errorMessages], 400);
                }
    
                $log->update([
                    'user_id' => $request->input('user_id'),
                    'senddate' => $request->input('senddate'),
                    'event_id' => $request->input('event_id'),
                    'sent' => $request->input('sent'),
                ]);
    
                return response()->json($log, 200);
            } else {
                return response()->json(['message' => 'Không tìm thấy log ' . $id], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Có lỗi xảy ra: Logs chưa được chỉnh sửa.'. $e->getMessage()], 500);
        }
    }

    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'senddate' => 'required|date_format:Y-m-d',
                'event_id' => ['required', Rule::exists('events', 'id')],
                'sent' => 'required',
            ], [
                'user_id.required' => 'Vui lòng nhập userId',
                'senddate.date_format' => 'Định dạng ngày không đúng.',
                'event_id.exists' => 'Event ID không tồn tại.',
                'required' => 'Vui lòng nhập đầy đủ thông tin',
                'senddate.required' => 'Vui lòng nhập ngày gửi',
                'event_id.required' => 'Vui lòng nhập id của event',
                'sent.required' => 'Vui lòng nhập cách gửi'
            ]);
            
            if ($validator->fails()) {
                $errors = $validator->errors();
                $errorMessages = [];
            
                if ($errors->has('user_id')) {
                    $errorMessages['user_id'] = $errors->first('user_id');
                }
            
                if ($errors->has('senddate')) {
                    $errorMessages['senddate'] = $errors->first('senddate');
                }
            
                if ($errors->has('event_id')) {
                    $errorMessages['event_id'] = $errors->first('event_id');
                }
            
                if ($errors->has('sent')) {
                    $errorMessages['sent'] = $errors->first('sent');
                }
            
                return response()->json(['message' => $errorMessages], 400);
            }

            $log = Logs::create([
                'user_id' => $request->input('user_id'),
                'senddate' => $request->input('senddate'),
                'event_id' => $request->input('event_id'),
                'sent' => $request->input('sent'),
            ]);

            return response()->json($log, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Có lỗi xảy ra: ' . $e->getMessage()], 500);
        }
    }

    public function delete($id)
    {
        $log = Logs::find($id);

        if ($log) {
            try {
                $log->delete();
                return response()->json(['message' => 'Log ' . $id . ' đã được xóa thành công']);
            } catch (\Exception $e) {
                return response()->json(['message' => 'Lỗi xóa log: ' . $e->getMessage()], 500);
            }
        } else {
            return response()->json(['message' => 'Không tìm thấy log có ID ' . $id], 404);
        }
    }
}
