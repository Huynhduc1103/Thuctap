<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Template;
use App\Models\Status;
use App\Models\Message;
use App\Models\Logs;

class SearchController extends Controller
{

    public function searchByUser(Request $request)
    {
        $keyword = $request->input('keyword');

        $users = User::where('email', 'like', '%' . $keyword . '%')
            ->orWhere('phone', 'like', '%' . $keyword . '%')
            ->orWhere('id', 'like', '%' . $keyword . '%')
            ->orWhere('birthday', 'like', '%' . $keyword . '%')
            ->get();

        if ($users->isEmpty()) {
            return response()->json(['error' => 'No users found.'], 404);
        }

        return response()->json(['users' => $users], 200);
    }

    public function searchByTemplade(Request $request)
    {
        $keyword = $request->input('keyword');

        $template = Template::where('id', 'like', '%' . $keyword . '%')
            ->orWhere('notification', 'like', '%' . $keyword . '%')
            ->orWhere('content', 'like', '%' . $keyword . '%')
            ->get();

        if ($template->isEmpty()) {
            return response()->json(['error' => 'No users found.'], 404);
        }

        return response()->json(['template' => $template], 200);
    }
    
    
    public function searchByLogs(Request $request)
    {
        $keyword = $request->input('keyword');

        $logs = Logs::where('id', 'like', '%' . $keyword . '%')
            ->orWhere('user_id', 'like', '%' . $keyword . '%')
            ->orWhere('senddate', 'like', '%' . $keyword . '%')
            ->orWhere('status_id', 'like', '%' . $keyword . '%')
            ->get();

        if ($logs->isEmpty()) {
            return response()->json(['error' => 'No users found.'], 404);
        }

        return response()->json(['template' => $logs], 200);
    }
}
