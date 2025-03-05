<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    public function index() {
        return response()->json(Task::all(), 200);
    }

    public function show($code) {
        $task = Task::where('code', $code)->first();
        if (!$task) return response()->json(['message' => 'Task not found'], 404);
        return response()->json($task, 200);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'code' => 'required|unique:tasks',
            'task_name' => 'required',
            'assigned_by' => 'required|exists:users,nik',
            'assigned_to' => 'required|exists:users,nik',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $task = Task::create($request->all());
        return response()->json($task, 201);
    }

    public function updateStatus($code, $status) {
        $task = Task::where('code', $code)->first();
        if (!$task) return response()->json(['message' => 'Task not found'], 404);

        if (!in_array($status, ['ongoing', 'pending', 'completed'])) {
            return response()->json(['message' => 'Invalid status'], 400);
        }

        $task->update(['status' => $status]);
        return response()->json($task, 200);
    }

    public function destroy($code) {
        $task = Task::where('code', $code)->first();
        if (!$task) return response()->json(['message' => 'Task not found'], 404);

        $task->delete();
        return response()->json(['message' => 'Task deleted'], 200);
    }
}
