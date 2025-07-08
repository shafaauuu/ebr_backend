<?php

namespace App\Http\Controllers\FormD;

use App\Http\Controllers\Controller;
use App\Models\FormD\MachineFcsShi;
use App\Models\FormD\FormD;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FormDMachineFcsShiController extends Controller
{
    /**
     * Display machine FCS SHI data based on task ID.
     *
     * @param  int  $taskId
     * @return \Illuminate\Http\Response
     */
    public function show($taskId)
    {
        try {
            // Find machine FCS SHI data by task ID
            $machineFcsShi = MachineFcsShi::where('task_id', $taskId)
                ->with(['formD', 'task'])
                ->orderBy('id_machine_fsc_assy', 'desc')
                ->first();

            if (!$machineFcsShi) {
                return response()->json([
                    'message' => 'No Machine FCS SHI data found for this task',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'message' => 'Machine FCS SHI data retrieved successfully',
                'data' => $machineFcsShi
            ]);
        } catch (\Exception $e) {
            \Log::error('Error retrieving Machine FCS SHI data: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to retrieve Machine FCS SHI data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store or update machine FCS SHI data based on task ID.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Log the incoming request for debugging
        \Log::info('Machine FCS SHI Request:', $request->all());

        $validator = Validator::make($request->all(), [
            'code_task' => 'required|string|max:50',
            'temp_nozzle_z1' => 'nullable|numeric',
            'temp_nozzle_z2' => 'nullable|numeric',
            'temp_nozzle_z3' => 'nullable|numeric',
            'temp_nozzle_z4' => 'nullable|numeric',
            'temp_nozzle_z5' => 'nullable|numeric',
            'temp_mold' => 'nullable|numeric',
            'inject_pressure' => 'nullable|numeric',
            'inject_time' => 'nullable|numeric',
            'holding_pressure' => 'nullable|numeric',
            'holding_time' => 'nullable|numeric',
            'eject_counter' => 'nullable|numeric',
            'cycle_time' => 'nullable|numeric',
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
            $machineFcsShi = MachineFcsShi::create($request->all());
            $action = 'ADD MACHINE FCS SHI';
            $message = 'Machine FCS SHI data created successfully';

            // Log the action
            Log::create([
                'action' => $action,
                'created_date' => now(),
                'created_by' => $request->user() ? $request->user()->nik : 'system',
                'created_at' => now(),
                'task_id' => $request->task_id,
                'details' => json_encode([
                    'machine_fcs_shi_id' => $machineFcsShi->id_machine_fsc_assy,
                    'form_d_id' => $request->form_d_id,
                ]),
            ]);

            DB::commit();

            return response()->json([
                'message' => $message,
                'data' => $machineFcsShi
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error submitting Machine FCS SHI data: ' . $e->getMessage());
            \Log::error('Error trace: ' . $e->getTraceAsString());

            return response()->json([
                'message' => 'Failed to save Machine FCS SHI data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
