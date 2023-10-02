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
    $keyword = $request -> input('keyword');

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
    $keyword = $request -> input('keyword');

    $template = Template::where('id', 'like', '%' . $keyword . '%')
                 ->orWhere('notification', 'like', '%' . $keyword . '%')
                 ->orWhere('content', 'like', '%' . $keyword . '%')
                 ->get();

    if ($template->isEmpty()) {
        return response()->json(['error' => 'No users found.'], 404);
    }

    return response()->json(['template' => $template], 200);
}
public function searchByStatus(Request $request)
{
    $keyword = $request -> input('keyword');

    $status = Status::where('id', 'like', '%' . $keyword . '%')
                 ->orWhere('statusmessage', 'like', '%' . $keyword . '%')
                 ->get();

    if ($status->isEmpty()) {
        return response()->json(['error' => 'No users found.'], 404);
    }

    return response()->json(['template' => $status], 200);
}
public function searchByMessage(Request $request)
{
    $keyword = $request -> input('keyword');

    $message = Message::where('id', 'like', '%' . $keyword . '%')
                 ->orWhere('eventname', 'like', '%' . $keyword . '%')
                 ->orWhere('desribe', 'like', '%' . $keyword . '%')
                 ->orWhere('eventdate', 'like', '%' . $keyword . '%')
                 ->orWhere('template_id', 'like', '%' . $keyword . '%')
                 ->get();

    if ($message->isEmpty()) {
        return response()->json(['error' => 'No users found.'], 404);
    }

    return response()->json(['template' => $message], 200);
}
public function searchByLogs(Request $request)
{
    $keyword = $request -> input('keyword');

    $logs = Logs::where('id', 'like', '%' . $keyword . '%')
                 ->orWhere('user_id', 'like', '%' . $keyword . '%')
                 ->orWhere('message_id', 'like', '%' . $keyword . '%')
                 ->orWhere('senddate', 'like', '%' . $keyword . '%')
                 ->orWhere('status_id', 'like', '%' . $keyword . '%')
                 ->get();

    if ($logs->isEmpty()) {
        return response()->json(['error' => 'No users found.'], 404);
    }

    return response()->json(['template' => $logs], 200);
}
}
