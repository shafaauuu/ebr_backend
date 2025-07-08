<?php

namespace App\Http\Controllers\FormD;

use App\Http\Controllers\Controller;
use App\Models\FormD\MachineSgp;
use App\Models\FormD\FormD;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FormDMachineSgpController extends Controller
{
    /**
     * Display machine SGP data based on task ID.
     *
     * @param  int  $taskId
     * @return \Illuminate\Http\Response
     */
    public function show($taskId)
    {
        try {
            // Find machine SGP data by task ID
            $machineSgp = MachineSgp::where('task_id', $taskId)
                ->with(['formD', 'task'])
                ->orderBy('id_machine_fcs', 'desc')
                ->first();

            if (!$machineSgp) {
                return response()->json([
                    'message' => 'No Machine SGP data found for this task',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'message' => 'Machine SGP data retrieved successfully',
                'data' => $machineSgp
            ]);
        } catch (\Exception $e) {
            \Log::error('Error retrieving Machine SGP data: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to retrieve Machine SGP data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store or update machine SGP data based on task ID.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Log the incoming request for debugging
        \Log::info('Machine SGP Request:', $request->all());

        $validator = Validator::make($request->all(), [
            'code_task' => 'required|string|max:50',
            'temp1' => 'nullable|numeric',
            'temp2' => 'nullable|numeric',
            'temp3' => 'nullable|numeric',
            'temp4' => 'nullable|numeric',
            'load_cap' => 'nullable|boolean',
            'load_hub' => 'nullable|boolean',
            'load_needle' => 'nullable|boolean',
            'hasil_epoxy' => 'nullable|boolean',
            'pressure_actual' => 'nullable|numeric',
            'pressure_status' => 'nullable|boolean',
            'low_epoxy1' => 'nullable|boolean',
            'low_epoxy2' => 'nullable|boolean',
            'low_epoxy3' => 'nullable|boolean',
            'hub_cannula1' => 'nullable|boolean',
            'hub_cannula2' => 'nullable|boolean',
            'hub_cannula3' => 'nullable|boolean',
            'exc_epoxy1' => 'nullable|boolean',
            'exc_epoxy2' => 'nullable|boolean',
            'exc_epoxy3' => 'nullable|boolean',
            'needle_tumpul1' => 'nullable|boolean',
            'needle_tumpul2' => 'nullable|boolean',
            'needle_tumpul3' => 'nullable|boolean',
            'needle_balik1' => 'nullable|boolean',
            'needle_balik2' => 'nullable|boolean',
            'needle_balik3' => 'nullable|boolean',
            'needle_tersumbat1' => 'nullable|boolean',
            'needle_tersumbat2' => 'nullable|boolean',
            'needle_tersumbat3' => 'nullable|boolean',
            'masterbatch' => 'nullable|integer',
            'berat_product' => 'nullable|numeric',
            'cavity' => 'nullable|integer',
            'berat_produk' => 'nullable|numeric',
            'sampling' => 'nullable|integer',
            'defect' => 'nullable|integer',
            'approval_hasil_printing' => 'nullable',
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
            $machineSgp = MachineSgp::create($request->all());
            $action = 'ADD MACHINE SGP';
            $message = 'Machine SGP data created successfully';

            // Log the action
            Log::create([
                'action' => $action,
                'created_date' => now(),
                'created_by' => $request->user() ? $request->user()->nik : 'system',
                'created_at' => now(),
                'task_id' => $request->task_id,
                'details' => json_encode([
                    'machine_sgp_id' => $machineSgp->id_machine_fcs,
                    'form_d_id' => $request->form_d_id,
                ]),
            ]);

            DB::commit();

            return response()->json([
                'message' => $message,
                'data' => $machineSgp
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error submitting Machine SGP data: ' . $e->getMessage());
            \Log::error('Error trace: ' . $e->getTraceAsString());

            return response()->json([
                'message' => 'Failed to save Machine SGP data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
