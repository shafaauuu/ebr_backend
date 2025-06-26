<?php

namespace App\Http\Controllers\FormD;

use App\Http\Controllers\Controller;
use App\Models\FormD\MachineAssy;
use App\Models\FormD\FormD;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FormDMachineAssyController extends Controller
{
    /**
     * Display machine assy data based on task ID.
     *
     * @param  int  $taskId
     * @return \Illuminate\Http\Response
     */
    public function show($taskId)
    {
        try {
            // Find machine assy data by task ID
            $machineAssy = MachineAssy::where('task_id', $taskId)
                ->with(['formD', 'task'])
                ->orderBy('id_machine_assy', 'desc')
                ->first();

            if (!$machineAssy) {
                return response()->json([
                    'message' => 'No Machine Assy data found for this task',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'message' => 'Machine Assy data retrieved successfully',
                'data' => $machineAssy
            ]);
        } catch (\Exception $e) {
            \Log::error('Error retrieving Machine Assy data: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to retrieve Machine Assy data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store or update machine assy data based on task ID.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Log the incoming request for debugging
        \Log::info('Machine Assy Request:', $request->all());

        $validator = Validator::make($request->all(), [
            'code_task' => 'required|string|max:50',
            'actual_running' => 'nullable|numeric',
            'run_awal' => 'nullable|numeric',
            'defect' => 'nullable|numeric',
            'goods_ok' => 'nullable|numeric',
            'goods_reject' => 'nullable|numeric',
            'qc_barrel' => 'nullable|string',
            'qc_gasket' => 'nullable|string',
            'qc_plunger' => 'nullable|string',
//            'form_d_id' => 'required|exists:form_d,id_form_d',
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
            $existingRecord = MachineAssy::where('task_id', $request->task_id)->first();

            if ($existingRecord) {
                // Update existing record
                $existingRecord->update($request->all());
                $machineAssy = $existingRecord;
                $action = 'UPDATE MACHINE ASSY';
                $message = 'Machine Assy data updated successfully';
            } else {
                // Create new record
                $machineAssy = MachineAssy::create($request->all());
                $action = 'ADD MACHINE ASSY';
                $message = 'Machine Assy data created successfully';
            }

            // Log the action
            Log::create([
                'action' => $action,
                'created_date' => now(),
                'created_by' => $request->user() ? $request->user()->nik : 'system',
                'created_at' => now(),
                'task_id' => $request->task_id,
                'details' => json_encode([
                    'machine_assy_id' => $machineAssy->id_machine_assy,
                    'form_d_id' => $request->form_d_id,
                ]),
            ]);

            DB::commit();

            return response()->json([
                'message' => $message,
                'data' => $machineAssy
            ], $existingRecord ? 200 : 201);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error submitting Machine Assy data: ' . $e->getMessage());
            \Log::error('Error trace: ' . $e->getTraceAsString());

            return response()->json([
                'message' => 'Failed to save Machine Assy data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
