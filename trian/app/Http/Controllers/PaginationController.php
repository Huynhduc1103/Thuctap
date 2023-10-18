<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Template;
use App\Models\Status;
use App\Models\Message;
use App\Models\Logs;

class PaginationController extends Controller
{
    public function PagintionUser()
    {
        $users = User::paginate(5);
        return response()->json(['users' => $users], 200);
    }
    public function PagintionTemplate()
    {
        $template = Template::paginate(5);
        return response()->json(['template' => $template], 200);
    }
    public function PagintionMessage()
    {
        $message = Message::paginate(5);
        return response()->json(['message' => $message], 200);
    }
    public function PagintionLogs()
    {
        $logs = Logs::paginate(5);
        return response()->json(['logs' => $logs], 200);
    }
}