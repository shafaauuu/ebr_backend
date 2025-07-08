<?php

namespace App\Http\Controllers\FormD;

use App\Http\Controllers\Controller;
use App\Models\FormD\MachineFcs;
use App\Models\FormD\FormD;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FormDMachineFcsController extends Controller
{
    /**
     * Display machine FCS data based on task ID.
     *
     * @param  int  $taskId
     * @return \Illuminate\Http\Response
     */
    public function show($taskId)
    {
        try {
            // Find machine FCS data by task ID
            $machineFcs = MachineFcs::where('task_id', $taskId)
                ->with(['formD', 'task'])
                ->orderBy('id_machine_fcs', 'desc')
                ->first();

            if (!$machineFcs) {
                return response()->json([
                    'message' => 'No Machine FCS data found for this task',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'message' => 'Machine FCS data retrieved successfully',
                'data' => $machineFcs
            ]);
        } catch (\Exception $e) {
            \Log::error('Error retrieving Machine FCS data: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to retrieve Machine FCS data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store or update machine FCS data based on task ID.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Log the incoming request for debugging
        \Log::info('Machine FCS Request:', $request->all());

        $validator = Validator::make($request->all(), [
            'code_task' => 'required|string|max:50',
            'open_position_slow' => 'nullable|numeric',
            'open_position_fast' => 'nullable|numeric',
            'open_position_mid' => 'nullable|numeric',
            'open_position_dec' => 'nullable|numeric',
            'open_speed_slow' => 'nullable|numeric',
            'open_speed_fast' => 'nullable|numeric',
            'open_speed_mid' => 'nullable|numeric',
            'open_speed_dec' => 'nullable|numeric',
            'sealing_temperature' => 'nullable|numeric',
            'open_pressure_slow' => 'nullable|numeric',
            'open_pressure_fast' => 'nullable|numeric',
            'open_pressure_mid' => 'nullable|numeric',
            'open_pressure_dec' => 'nullable|numeric',
            'close_position_slow' => 'nullable|numeric',
            'close_position_fast' => 'nullable|numeric',
            'close_position_mid' => 'nullable|numeric',
            'close_position_dec' => 'nullable|numeric',
            'close_speed_slow' => 'nullable|numeric',
            'close_speed_fast' => 'nullable|numeric',
            'close_speed_mid' => 'nullable|numeric',
            'close_speed_dec' => 'nullable|numeric',
            'close_pressure_slow' => 'nullable|numeric',
            'close_pressure_fast' => 'nullable|numeric',
            'close_pressure_mid' => 'nullable|numeric',
            'close_pressure_dec' => 'nullable|numeric',
            'ejector_position_ret2' => 'nullable|numeric',
            'ejector_position_ret1' => 'nullable|numeric',
            'ejector_position_adv2' => 'nullable|numeric',
            'ejector_position_adv1' => 'nullable|numeric',
            'ejector_speed_ret2' => 'nullable|numeric',
            'ejector_speed_ret1' => 'nullable|numeric',
            'ejector_speed_adv2' => 'nullable|numeric',
            'ejector_speed_adv1' => 'nullable|numeric',
            'ejector_pressure_ret2' => 'nullable|numeric',
            'ejector_pressure_ret1' => 'nullable|numeric',
            'ejector_pressure_adv2' => 'nullable|numeric',
            'ejector_pressure_adv1' => 'nullable|numeric',
            'temperature_sv_sect1' => 'nullable|numeric',
            'temperature_sv_sect2' => 'nullable|numeric',
            'temperature_sv_sect3' => 'nullable|numeric',
            'temperature_sv_sect4' => 'nullable|numeric',
            'temperature_sv_sect5' => 'nullable|numeric',
            'temperature_sv_sect6' => 'nullable|numeric',
            'temperature_pv_sect1' => 'nullable|numeric',
            'temperature_pv_sect2' => 'nullable|numeric',
            'temperature_pv_sect3' => 'nullable|numeric',
            'temperature_pv_sect4' => 'nullable|numeric',
            'temperature_pv_sect5' => 'nullable|numeric',
            'temperature_pv_sect6' => 'nullable|numeric',
            'temperature_pre_sect1' => 'nullable|numeric',
            'temperature_pre_sect2' => 'nullable|numeric',
            'temperature_pre_sect3' => 'nullable|numeric',
            'temperature_pre_sect4' => 'nullable|numeric',
            'temperature_pre_sect5' => 'nullable|numeric',
            'temperature_pre_sect6' => 'nullable|numeric',
            'temperature_max_sect1' => 'nullable|numeric',
            'temperature_max_sect2' => 'nullable|numeric',
            'temperature_max_sect3' => 'nullable|numeric',
            'temperature_max_sect4' => 'nullable|numeric',
            'temperature_max_sect5' => 'nullable|numeric',
            'temperature_max_sect6' => 'nullable|numeric',
            'temperature_low_sect1' => 'nullable|numeric',
            'temperature_low_sect2' => 'nullable|numeric',
            'temperature_low_sect3' => 'nullable|numeric',
            'temperature_low_sect4' => 'nullable|numeric',
            'temperature_low_sect5' => 'nullable|numeric',
            'temperature_low_sect6' => 'nullable|numeric',
            'filling_position_inj5' => 'nullable|numeric',
            'filling_position_inj4' => 'nullable|numeric',
            'filling_position_inj3' => 'nullable|numeric',
            'filling_position_inj2' => 'nullable|numeric',
            'filling_position_inj1' => 'nullable|numeric',
            'filling_velocity_inj5' => 'nullable|numeric',
            'filling_velocity_inj4' => 'nullable|numeric',
            'filling_velocity_inj3' => 'nullable|numeric',
            'filling_velocity_inj2' => 'nullable|numeric',
            'filling_velocity_inj1' => 'nullable|numeric',
            'filling_pressure_inj5' => 'nullable|numeric',
            'filling_pressure_inj4' => 'nullable|numeric',
            'filling_pressure_inj3' => 'nullable|numeric',
            'filling_pressure_inj2' => 'nullable|numeric',
            'filling_pressure_inj1' => 'nullable|numeric',
            'filling_time_inj5' => 'nullable|numeric',
            'filling_time_inj4' => 'nullable|numeric',
            'filling_time_inj3' => 'nullable|numeric',
            'filling_time_inj2' => 'nullable|numeric',
            'filling_time_inj1' => 'nullable|numeric',
            'holding_speed_hdp4' => 'nullable|numeric',
            'holding_speed_hdp3' => 'nullable|numeric',
            'holding_speed_hdp2' => 'nullable|numeric',
            'holding_speed_hdp1' => 'nullable|numeric',
            'holding_pressure_hdp4' => 'nullable|numeric',
            'holding_pressure_hdp3' => 'nullable|numeric',
            'holding_pressure_hdp2' => 'nullable|numeric',
            'holding_pressure_hdp1' => 'nullable|numeric',
            'charging_back_pre' => 'nullable|numeric',
            'charging_back_charge1' => 'nullable|numeric',
            'charging_back_charge2' => 'nullable|numeric',
            'charging_back_charge3' => 'nullable|numeric',
            'charging_back_post' => 'nullable|numeric',
            'charging_speed_pre' => 'nullable|numeric',
            'charging_speed_charge1' => 'nullable|numeric',
            'charging_speed_charge2' => 'nullable|numeric',
            'charging_speed_charge3' => 'nullable|numeric',
            'charging_speed_post' => 'nullable|numeric',
            'charging_pressure_pre' => 'nullable|numeric',
            'charging_pressure_charge1' => 'nullable|numeric',
            'charging_pressure_charge2' => 'nullable|numeric',
            'charging_pressure_charge3' => 'nullable|numeric',
            'charging_pressure_post' => 'nullable|numeric',
            'charging_position_pre' => 'nullable|numeric',
            'charging_position_charge1' => 'nullable|numeric',
            'charging_position_charge2' => 'nullable|numeric',
            'charging_position_charge3' => 'nullable|numeric',
            'charging_position_post' => 'nullable|numeric',
            'purge_velocity_slow' => 'nullable|numeric',
            'purge_velocity_fast' => 'nullable|numeric',
            'purge_velocity_back' => 'nullable|numeric',
            'purge_pressure_slow' => 'nullable|numeric',
            'purge_pressure_back' => 'nullable|numeric',
            'purge_pressure_fast' => 'nullable|numeric',
            'purge_position_slow' => 'nullable|numeric',
            'purge_position_fast' => 'nullable|numeric',
            'purge_position_back' => 'nullable|numeric',
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
            $machineFcs = MachineFcs::create($request->all());
            $action = 'ADD MACHINE FCS';
            $message = 'Machine FCS data created successfully';

            // Log the action
            Log::create([
                'action' => $action,
                'created_date' => now(),
                'created_by' => $request->user() ? $request->user()->nik : 'system',
                'created_at' => now(),
                'task_id' => $request->task_id,
                'details' => json_encode([
                    'machine_fcs_id' => $machineFcs->id_machine_fcs,
                    'form_d_id' => $request->form_d_id,
                ]),
            ]);

            DB::commit();

            return response()->json([
                'message' => $message,
                'data' => $machineFcs
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error submitting Machine FCS data: ' . $e->getMessage());
            \Log::error('Error trace: ' . $e->getTraceAsString());

            return response()->json([
                'message' => 'Failed to save Machine FCS data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
