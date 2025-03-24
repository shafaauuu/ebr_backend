<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Log;
use Carbon\Carbon;

class LogController extends Controller
{
    // List all logs
    public function index()
    {
        return Log::all();
    }

    // Store a new log with auto-generated code_task
    public function store(Request $request)
    {
        $request->validate([
            'brm_no'    => 'required|string',
            'batch_no'  => 'required|string',
            'action'    => 'required|string',
            'created_by'=> 'required|string',
            'log_date'  => 'required|date', // Date used for code generation
        ]);

        // Generate code_task
        $date = Carbon::parse($request->log_date)->format('ymd'); // YYMMDD
        $code_task = $request->brm_no . $date . $request->batch_no;

        // Store log
        $log = Log::create([
            'code_task'    => $code_task,
            'action'       => $request->action,
            'created_date' => now(),
            'created_by'   => $request->created_by,
            'created_at'   => now(),
        ]);

        return response()->json(['message' => 'Log created', 'log' => $log]);
    }
}
