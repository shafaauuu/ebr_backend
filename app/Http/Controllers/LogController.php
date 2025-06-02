<?php

namespace App\Http\Controllers;

use App\Models\NoDoc; // Add this at the top
use App\Models\Log;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'brm_no'     => 'required|string',
            'batch_no'   => 'required|string',
            'action'     => 'required|string',
            'created_by' => 'required|string',
            'log_date'   => 'required|date', // for code generation
        ]);

        // Generate code_task (brm_no + date + batch_no)
        $date = Carbon::parse($request->log_date)->format('ymd'); // YYMMDD
        $code_task = $request->brm_no . $date . $request->batch_no;

        // Ensure the no_doc entry exists (insert if not exists)
        $noDoc = NoDoc::firstOrCreate(
            ['code_task' => $code_task],
            [
                'no_batch'   => $request->batch_no,
                'created_at' => now(),
            ]
        );

        // Create log entry
        $log = Log::create([
            'code_task'    => $code_task,
            'action'       => $request->action,
            'created_date' => now(),
            'created_by'   => $request->created_by,
            'created_at'   => now(),
        ]);

        return response()->json(['message' => 'Log created', 'log' => $log]);
    }

    public function show($id)
    {
        $task = NoDoc::with(['assignedByUser', 'assignedToUser'])->findOrFail($id);

        return response()->json([
            'code_task' => $task->code_task,
            'assigned_by_name' => $task->assignedByUser?->first_name,
            'assigned_to_email' => $task->assignedToUser?->email,
        ]);
    }

}
