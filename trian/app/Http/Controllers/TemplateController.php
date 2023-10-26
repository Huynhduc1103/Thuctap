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
        $timer = $request->input('timer');
        $type = '';
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
    
    

    public function findId(Request $request) {
        $id = $request->input('id');
        $template = Template::find($id);
        return response()->json($template);
    }

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

    public function delete($id) {
        $template = Template::find($id);
        try {
            // Xóa 
            $template->delete();
            // Xóa thành công
            return response()->json(['message' => 'template ' . $id . ' đã được xóa thành công']);
        } catch (\Exception $e) {
            // Xử lý lỗi nếu có
            return response()->json(['message' => 'Lỗi xóa template: ' . $e->getMessage()], 500);
        }
    }

}
