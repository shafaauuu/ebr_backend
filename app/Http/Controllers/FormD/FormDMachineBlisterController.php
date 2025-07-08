<?php

namespace App\Http\Controllers\FormD;

use App\Http\Controllers\Controller;
use App\Models\FormD\MachineBlister;
use App\Models\FormD\FormD;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FormDMachineBlisterController extends Controller
{
    /**
     * Display machine blister data based on task ID.
     *
     * @param  int  $taskId
     * @return \Illuminate\Http\Response
     */
    public function show($taskId)
    {
        try {
            // Find machine blister data by task ID
            $machineBlister = MachineBlister::where('task_id', $taskId)
                ->with(['formD', 'task'])
                ->orderBy('id_machine_blister', 'desc')
                ->first();

            if (!$machineBlister) {
                return response()->json([
                    'message' => 'No Machine Blister data found for this task',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'message' => 'Machine Blister data retrieved successfully',
                'data' => $machineBlister
            ]);
        } catch (\Exception $e) {
            \Log::error('Error retrieving Machine Blister data: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to retrieve Machine Blister data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store or update machine blister data based on task ID.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Log the incoming request for debugging
        \Log::info('Machine Blister Request:', $request->all());

        $validator = Validator::make($request->all(), [
            'code_task' => 'required|string|max:50',
            'forming_time' => 'nullable|string',
            'forming_temperature' => 'nullable|string',
            'forming_pressure' => 'nullable|string',
            'sealing_temperature' => 'nullable|string',
            'sealing_pressure' => 'nullable|string',
            'sealing_time' => 'nullable|string',
            'cycle_time' => 'nullable|string',
            'mfg_date' => 'nullable|date',
            'exp_date' => 'nullable|date',
            'needle_size' => 'nullable|string',
            'nie' => 'nullable|string',
            'approval_hasil_printing' => 'nullable|string',
            'form_d_id' => 'required|exists:form_d,id_form_d',
            'task_id' => 'required|exists:tasks,id',
            'machine_picture' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Check if there's an existing record for this task
            $existingRecord = MachineBlister::where('task_id', $request->task_id)->first();

            // Create new record
            $machineBlister = MachineBlister::create($request->all());
            $action = 'ADD MACHINE BLISTER';
            $message = 'Machine Blister data created successfully';

            // Log the action
            Log::create([
                'action' => $action,
                'created_date' => now(),
                'created_by' => $request->user() ? $request->user()->nik : 'system',
                'created_at' => now(),
                'task_id' => $request->task_id,
                'details' => json_encode([
                    'machine_blister_id' => $machineBlister->id_machine_blister,
                    'form_d_id' => $request->form_d_id,
                ]),
            ]);

            DB::commit();

            return response()->json([
                'message' => $message,
                'data' => $machineBlister
            ], $existingRecord ? 200 : 201);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error submitting Machine Blister data: ' . $e->getMessage());
            \Log::error('Error trace: ' . $e->getTraceAsString());

            return response()->json([
                'message' => 'Failed to save Machine Blister data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
