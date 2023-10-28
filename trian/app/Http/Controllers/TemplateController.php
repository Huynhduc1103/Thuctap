<?php

namespace App\Http\Controllers;

use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;

class TemplateController extends Controller
{
    public function read() {
        $template = Template::all();
        return response()->json($template);
    }


    public function create(Request $request) {
<<<<<<< HEAD
        $timer = $request->input('timer');
        $type = '';
=======
        
        $timer = $request->input('timer'); // Không được lấy ngày giờ hiện tại nghen
>>>>>>> 32a194add7398c977770877b2f1faee75ca448dc
        $data = $request->input('data');
        $event_id = $request->input('event_id');
        
        // Tự động xác định giá trị cho trường "type" dựa trên dữ liệu
        $dataArray = explode(',', $data);
        if (count($dataArray) == 1) {
            $type = 'USER';
        } elseif (count($dataArray) >= 2) {
            $type = 'GROUP';
        } else {
            return response()->json(['error' => 'Dữ liệu không hợp lệ.'], 400);
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
    

    public function update($id, Request $request) {
        $template = Template::find($id);
    
        if (!$template) {
            return response()->json(['error' => 'Sự kiện không tồn tại.'], 404);
        }
    
        $timer = $request->input('timer'); // Không được lấy ngày giờ hiện tại nghen
        $data = $request->input('data');
        $message_id = $request->input('message_id');
        $event_id = $request->input('event_id');
    
        // Tự động xác định giá trị cho trường "type" dựa trên dữ liệu
        $dataArray = explode(',', $data);
        if (count($dataArray) == 1) {
            $type = 'USER';
        } elseif (count($dataArray) >= 2) {
            $type = 'GROUP';
        } else {
            return response()->json(['error' => 'Dữ liệu không hợp lệ.'], 400);
        }
    
        // Cập nhật dữ liệu cho sự kiện có ID tương ứng
        $template->timer = $timer;
        $template->type = $type;
        $template->data = $data;
        $template->message_id = $message_id;
        $template->event_id = $event_id;
        $template->save();
    
        return response()->json($template);
    }
    
       
    public function findId(Request $request) {
        $id = $request->input('id');
        $template = Template::find($id);
        return response()->json($template);
    }

<<<<<<< HEAD
    public function update($id, Request $request) {
        $template = Template::find($id);

        $timer = $request->input('timer');
        $type = $request->input('type');
        $data = $request->input('data');
        $event_id = $request->input('event_id');
    
        if ($type === 'GROUP') {
            // Kiểm tra xem dữ liệu có ít nhất 2 dữ liệu trở lên (sử dụng dấu phẩy để phân tách)
            $dataArray = explode(',', $data);
            if (count($dataArray) < 2) {
                return response()->json(['error' => 'Dữ liệu phải chứa ít nhất 2 dữ liệu nếu loại là "GROUP".'], 400);
            }
        } elseif ($type === 'USER') {
            // Kiểm tra xem dữ liệu chỉ chứa một giá trị
            $dataArray = explode(',', $data);
            if (count($dataArray) != 1) {
                return response()->json(['error' => 'Dữ liệu chỉ được phép chứa một giá trị nếu loại là "USER".'], 400);
            }
        } else {
            return response()->json(['error' => 'Type không hợp lệ. Type phải là "GROUP" hoặc "USER".'], 400);
        }
    
        // Sau khi kiểm tra, tạo sự kiện nếu không có lỗi
        $event = Template::create([
            'timer' => $timer,
            'type' => $type,
            'data' => $data,
            'event_id' => $event_id,
        ]);
    
        return response()->json($event);
    }
=======
>>>>>>> 32a194add7398c977770877b2f1faee75ca448dc

    public function delete($id) {
        $template = Template::find($id);
        try {
            // Xóa 
            $template->delete();
            // Xóa thành công
            return response()->json(['message' => 'Template ' . $id . ' đã được xóa thành công']);
        } catch (\Exception $e) {
            // Xử lý lỗi nếu có
            return response()->json(['message' => 'Lỗi xóa template: ' . $e->getMessage()], 500);
        }
    }

}
