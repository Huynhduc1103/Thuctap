<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Failed;
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
            ->orWhere('fullname', 'like', '%' . $keyword . '%')
            ->orWhere('groupcode', 'like', '%' . $keyword . '%')
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
            ->orWhere('timer', 'like', '%' . $keyword . '%')
            ->orWhere('type', 'like', '%' . $keyword . '%')
            ->orWhere('data', 'like', '%' . $keyword . '%')
            ->orWhere('event_id', 'like', '%' . $keyword . '%')
            ->get();

        if ($template->isEmpty()) {
            return response()->json(['error' => 'No template found.'], 404);
        }

        return response()->json(['template' => $template], 200);
    }
    public function searchByLogs(Request $request)
    {
        $keyword = $request->input('keyword');

        $logs = Logs::where('id', 'like', '%' . $keyword . '%')
            ->orWhere('user_id', 'like', '%' . $keyword . '%')
            ->orWhere('senddate', 'like', '%' . $keyword . '%')
            ->orWhere('event_id', 'like', '%' . $keyword . '%')
            ->orWhere('sent', 'like', '%' . $keyword . '%')
            ->get();

        if ($logs->isEmpty()) {
            return response()->json(['error' => 'No logs found.'], 404);
        }

        return response()->json(['logs' => $logs], 200);
    }
    public function searchByFailed(Request $request)
    {
        $keyword = $request->input('keyword');

        $failed = Failed::where('id', 'like', '%' . $keyword . '%')
            ->orWhere('user_id', 'like', '%' . $keyword . '%')
            ->orWhere('date', 'like', '%' . $keyword . '%')
            ->orWhere('event_id', 'like', '%' . $keyword . '%')
            ->orWhere('type', 'like', '%' . $keyword . '%')
            ->orWhere('error', 'like', '%' . $keyword . '%')
            ->get();

        if ($failed->isEmpty()) {
            return response()->json(['error' => 'No failed found.'], 404);
        }

        return response()->json(['failed' => $failed], 200);
    }
    public function searchByEvent(Request $request)
    {
        $keyword = $request->input('keyword');

        $event = Event::where('id', 'like', '%' . $keyword . '%')
            ->orWhere('eventname', 'like', '%' . $keyword . '%')
            ->orWhere('eventdate', 'like', '%' . $keyword . '%')
            ->get();

        if ($event->isEmpty()) {
            return response()->json(['error' => 'No event found.'], 404);
        }

        return response()->json(['event' => $event], 200);
    }
}
