<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use App\Models\UserRole;
use App\Models\RoleAuth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    public function index_task()
    {
        $tasks = Task::with('masterBrm')->get();

        // Format the response
        $tasks = $tasks->map(function ($task) {
            return [
                'id' => $task->id,
                'brm_no' => $task->masterBrm->brm_no ?? 'No BRM',
                'product_code' => $task->masterBrm->product_code ?? 'No Code',
                'product_name' => $task->masterBrm->product_name ?? 'Unnamed Task',
                'status' => $task->status,
                'assigned_by' => $task->assigned_by,
                'assigned_to' => $task->assigned_to,
                'no_batch' => $task->no_batch,
                'created_at' => $task->created_at,
                'updated_at' => $task->updated_at
            ];
        });

        return response()->json($tasks, 200);
    }

    public function show_task($id)
    {
        $task = Task::with('masterBrm')->find($id);

        if (!$task) return response()->json(['message' => 'Task not found'], 404);

        $formattedTask = [
            'id' => $task->id,
            'brm_no' => $task->masterBrm->brm_no ?? 'No BRM',
            'product_code' => $task->masterBrm->product_code ?? 'No Code',
            'product_name' => $task->masterBrm->product_name ?? 'Unnamed Task',
            'status' => $task->status,
            'assigned_by' => $task->assigned_by,
            'assigned_to' => $task->assigned_to,
            'no_batch' => $task->no_batch,
            'created_at' => $task->created_at,
            'updated_at' => $task->updated_at
        ];

        return response()->json($formattedTask, 200);
    }

    public function store_task(Request $request) {
        $validator = Validator::make($request->all(), [
            'id_brm' => 'required|exists:master_brms,id_brm',
            'assigned_by' => 'required|exists:users,nik',
            'assigned_to' => 'required|exists:users,nik',
            'no_batch' => 'required|string|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $task = Task::create($request->all());
        return response()->json($task, 201);
    }

    public function updateStatus_task($id, $status) {
        $task = Task::find($id);
        if (!$task) return response()->json(['message' => 'Task not found'], 404);

        if (!in_array($status, ['ongoing', 'pending', 'completed'])) {
            return response()->json(['message' => 'Invalid status'], 400);
        }

        $task->update(['status' => $status]);
        return response()->json($task, 200);
    }

    public function destroy_task($id) {
        $task = Task::find($id);
        if (!$task) return response()->json(['message' => 'Task not found'], 404);

        $task->delete();
        return response()->json(['message' => 'Task deleted'], 200);
    }

    /**
     * Reassign a task to another user during shift change
     */
    public function reassignTask(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            'assigned_to' => 'required|exists:users,nik',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $task = Task::find($id);
        if (!$task) return response()->json(['message' => 'Task not found'], 404);

        // Get the authenticated user
        $user = $request->user();
        
        // If no authenticated user (when auth is disabled), use the assigned_to as the creator
        $createdBy = $user ? $user->nik : $request->assigned_to;
        
        // Get the previous assignee for logging
        $previousAssignee = $task->assigned_to;

        // Begin transaction
        DB::beginTransaction();
        
        try {
            // Update the assigned_to field
            $task->update([
                'assigned_to' => $request->assigned_to,
                'updated_at' => now()
            ]);
            
            // Create log entry for the task reassignment
            \App\Models\Log::create([
                'task_id' => $task->id,
                'action' => "Task reassigned from user {$previousAssignee} to user {$request->assigned_to}",
                'created_by' => $createdBy,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            DB::commit();
            
            return response()->json([
                'message' => 'Task reassigned successfully',
                'task' => $task
            ], 200);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'Failed to reassign task',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Submit task for verification by head section
     */
    public function submitForVerification(Request $request, $id) {
        $task = Task::find($id);
        if (!$task) return response()->json(['message' => 'Task not found'], 404);

        // Change status to pending
        $task->status = 'pending';

        // Find head section user for the department
        $headSectionUser = $this->findHeadSectionUser();
        
        if (!$headSectionUser) {
            return response()->json(['message' => 'Head section user not found'], 404);
        }

        // Reassign the task to head section
        $task->assigned_to = $headSectionUser->nik;
        $task->save();

        return response()->json([
            'message' => 'Task submitted for verification',
            'task' => $task
        ], 200);
    }

    /**
     * Find a user with head section role
     */
    private function findHeadSectionUser() {
        // Find the role_auth_id for head section role
        $headSectionRole = RoleAuth::where('role', 'head_section')->first();
        
        if (!$headSectionRole) {
            return null;
        }

        // Find a user with this role
        $userRole = UserRole::where('role_auth_id', $headSectionRole->role_auth_id)->first();
        
        if (!$userRole) {
            return null;
        }

        // Get the user
        return User::where('nik', $userRole->user_nik)->first();
    }

    /**
     * Find head section users by department
     */
    public function findHeadSectionByDept(Request $request) {
        $validator = Validator::make($request->all(), [
            'dept' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Find the role_auth_id for head section role
        $headSectionRole = RoleAuth::where('role', 'head_section')->first();
        
        if (!$headSectionRole) {
            return response()->json(['message' => 'Head section role not found'], 404);
        }

        // Find users with this role in the specified department
        $headSectionUsers = User::join('user_role', 'users.nik', '=', 'user_role.user_nik')
            ->where('user_role.role_auth_id', $headSectionRole->role_auth_id)
            ->where('users.dept', $request->dept)
            ->select('users.*')
            ->get();

        return response()->json($headSectionUsers, 200);
    }

    /**
     * Get tasks assigned to the authenticated user
     */
    public function getMyTasks() {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $tasks = Task::with('masterBrm')
            ->where('assigned_to', $user->nik)
            ->get()
            ->map(function ($task) {
                return [
                    'id' => $task->id,
                    'brm_no' => $task->masterBrm->brm_no ?? 'No BRM',
                    'product_code' => $task->masterBrm->product_code ?? 'No Code',
                    'product_name' => $task->masterBrm->product_name ?? 'Unnamed Task',
                    'status' => $task->status,
                    'assigned_by' => $task->assigned_by,
                    'assigned_to' => $task->assigned_to,
                    'no_batch' => $task->no_batch,
                    'created_at' => $task->created_at,
                    'updated_at' => $task->updated_at
                ];
            });

        return response()->json($tasks, 200);
    }

    /**
     * Transfer tasks to users in a selected shift group
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function transferTasksToShiftGroup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tasks' => 'required|array',
            'tasks.*' => 'exists:tasks,id',
            'shift_group' => 'required_without:group|string|max:1',
            'group' => 'required_without:shift_group|string|max:1',
            'assigned_to' => 'required|exists:users,nik',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Get the authenticated user
        $user = $request->user();
        
        // If no authenticated user (when auth is disabled), use the assigned_to as the creator
        $createdBy = $user ? $user->nik : $request->assigned_to;
        
        // Get the shift group (either from shift_group or group parameter)
        $shiftGroup = $request->shift_group ?? $request->group;
        
        // Begin transaction
        DB::beginTransaction();
        
        try {
            // Update all specified tasks
            foreach ($request->tasks as $taskId) {
                $task = Task::find($taskId);
                
                // Skip if task not found
                if (!$task) {
                    continue;
                }
                
                // Get the previous assignee for logging
                $previousAssignee = $task->assigned_to;
                
                // Update the task
                $task->update([
                    'assigned_to' => $request->assigned_to,
                    'updated_at' => now()
                ]);
                
                // Create log entry for the task transfer
                \App\Models\Log::create([
                    'task_id' => $task->id,
                    'action' => "Task transferred from user {$previousAssignee} to user {$request->assigned_to} in shift group {$shiftGroup}",
                    'created_by' => $createdBy,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            DB::commit();
            
            return response()->json([
                'message' => 'Tasks successfully transferred to the next shift group',
                'shift_group' => $shiftGroup,
                'assigned_to' => $request->assigned_to
            ], 200);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'Failed to transfer tasks',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
