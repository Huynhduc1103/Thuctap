<?php

namespace App\Http\Controllers;

use App\Models\Template;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    public function read() {
        $template = Template::all();
        return response()->json($template);
    }

    public function create(Request $request) {
        $event = Template::create([
            'timer' => $request->input('timer'),
            'type' => $request->input('type'),
            'data' => $request->input('data'),
            'message_id' => $request->input('message_id'),
            'event_id' => $request->input('event_id'),

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
        $template->update([
            'timer' => $request->input('timer'),
            'type' => $request->input('type'),
            'data' => $request->input('data'),
            'message_id' => $request->input('message_id'),
            'event_id' => $request->input('event_id'),
        ]);
        return response()->json($template);
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
