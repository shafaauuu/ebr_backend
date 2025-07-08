<?php

namespace App\Http\Controllers\FormD;

use App\Http\Controllers\Controller;
use App\Models\FormD\MachineShi2;
use App\Models\FormD\FormD;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FormDMachineShi2Controller extends Controller
{
    /**
     * Display machine SHI 2 data based on task ID.
     *
     * @param  int  $taskId
     * @return \Illuminate\Http\Response
     */
    public function show($taskId)
    {
        try {
            // Find machine SHI 2 data by task ID
            $machineShi2 = MachineShi2::where('task_id', $taskId)
                ->with(['formD', 'task'])
                ->orderBy('id_machine_shi_2', 'desc')
                ->first();

            if (!$machineShi2) {
                return response()->json([
                    'message' => 'No Machine SHI 2 data found for this task',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'message' => 'Machine SHI 2 data retrieved successfully',
                'data' => $machineShi2
            ]);
        } catch (\Exception $e) {
            \Log::error('Error retrieving Machine SHI 2 data: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to retrieve Machine SHI 2 data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store or update machine SHI 2 data based on task ID.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Log the incoming request for debugging
        \Log::info('Machine SHI 2 Request:', $request->all());

        $validator = Validator::make($request->all(), [
            'code_task' => 'required|string|max:50',
            'open_position_openlimit' => 'nullable|numeric',
            'open_position_forth' => 'nullable|numeric',
            'open_position_third' => 'nullable|numeric',
            'open_position_second' => 'nullable|numeric',
            'open_position_first' => 'nullable|numeric',
            'open_velocity_limit' => 'nullable|numeric',
            'open_velocity_forth' => 'nullable|numeric',
            'open_velocity_third' => 'nullable|numeric',
            'open_velocity_second' => 'nullable|numeric',
            'open_velocity_first' => 'nullable|numeric',
            'close_position_limit' => 'nullable|numeric',
            'close_position_forth' => 'nullable|numeric',
            'close_position_third' => 'nullable|numeric',
            'close_position_second' => 'nullable|numeric',
            'close_position_first' => 'nullable|numeric',
            'close_velocity_limit' => 'nullable|numeric',
            'close_velocity_forth' => 'nullable|numeric',
            'close_velocity_third' => 'nullable|numeric',
            'close_velocity_second' => 'nullable|numeric',
            'close_velocity_first' => 'nullable|numeric',
            'temperature_z1' => 'nullable|numeric',
            'temperature_z2' => 'nullable|numeric',
            'temperature_z3' => 'nullable|numeric',
            'temperature_z4' => 'nullable|numeric',
            'temperature_z5' => 'nullable|numeric',
            'filling_position_vp' => 'nullable|numeric',
            'filling_position_first' => 'nullable|numeric',
            'filling_velocity_vp' => 'nullable|numeric',
            'filling_velocity_first' => 'nullable|numeric',
            'holding_time_second' => 'nullable|numeric',
            'holding_time_first' => 'nullable|numeric',
            'holding_pressure_second' => 'nullable|numeric',
            'holding_pressure_first' => 'nullable|numeric',
            'plastictizing_pullback_position' => 'nullable|numeric',
            'plastictizing_pullback_velocity' => 'nullable|numeric',
            'plastictizing_dose1_position' => 'nullable|numeric',
            'plastictizing_dose1_backpress' => 'nullable|numeric',
            'plastictizing_dose1_rotation' => 'nullable|numeric',
            'plastictizing_end_position' => 'nullable|numeric',
            'plastictizing_end_backpress' => 'nullable|numeric',
            'plastictizing_end_rotation' => 'nullable|numeric',
            'plastictizing_forward_position' => 'nullable|numeric',
            'plastictizing_forward_velocity' => 'nullable|numeric',
            'plastictizing_cooling' => 'nullable|numeric',
            'masterbatch' => 'nullable|integer',
            'berat_produk' => 'nullable|numeric',
            'berat_runner' => 'nullable|numeric',
            'cavity' => 'nullable|integer',
            'sampling' => 'nullable|integer',
            'defect' => 'nullable|integer',
            'form_d_id' => 'required|exists:form_d,id_form_d',
            'task_id' => 'required|exists:tasks,id',
            'machine_picture' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Create new record
            $machineShi2 = MachineShi2::create($request->all());
            $action = 'ADD MACHINE SHI 2';
            $message = 'Machine SHI 2 data created successfully';

            // Log the action
            Log::create([
                'action' => $action,
                'created_date' => now(),
                'created_by' => $request->user() ? $request->user()->nik : 'system',
                'created_at' => now(),
                'task_id' => $request->task_id,
                'details' => json_encode([
                    'machine_shi_2_id' => $machineShi2->id_machine_shi_2,
                    'form_d_id' => $request->form_d_id,
                ]),
            ]);

            DB::commit();

            return response()->json([
                'message' => $message,
                'data' => $machineShi2
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error submitting Machine SHI 2 data: ' . $e->getMessage());
            \Log::error('Error trace: ' . $e->getTraceAsString());

            return response()->json([
                'message' => 'Failed to save Machine SHI 2 data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
