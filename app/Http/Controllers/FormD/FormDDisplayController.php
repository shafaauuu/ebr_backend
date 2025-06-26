<?php

namespace App\Http\Controllers\FormD;

use App\Http\Controllers\Controller;
use App\Models\FormD\DisplayMachineAssy;
use App\Models\FormD\DisplayMachineFcs;
use App\Models\FormD\DisplayMachineFcsShi;
use App\Models\FormD\DisplayMachineShi1;
use App\Models\FormD\DisplayMachineShi2;
use App\Models\FormD\FormDDisplay;
use App\Models\Log;
use App\Models\MasterMachine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FormDDisplayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $displays = FormDDisplay::with(['machine'])->get();
        return response()->json($displays);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Log the incoming request for debugging
        \Log::info('Form D Display Request:', $request->all());

        $validator = Validator::make($request->all(), [
            'machine_id' => 'required|exists:master_machines,id_machine',
            'material_type' => 'required|string|max:10',
            'setting_march' => 'required|string',
            'shift' => 'required|string|max:2',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();

        try {
            $formData = [
                'machine_id' => $request->machine_id,
                'material_type' => $request->material_type,
                'setting_march' => $request->setting_march,
                'shift' => $request->shift,
            ];

            $display = FormDDisplay::create($formData);

            // Log the submission
            Log::create([
                'action' => 'ADD FORM D DISPLAY',
                'created_date' => now(),
                'created_by' => $request->user() ? $request->user()->nik : 'system',
                'created_at' => now(),
                'details' => json_encode([
                    'display_id' => $display->id_form_d_display,
                ]),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Form D Display created successfully',
                'data' => $display
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error submitting Form D Display: ' . $e->getMessage());
            \Log::error('Error trace: ' . $e->getTraceAsString());

            return response()->json([
                'message' => 'Failed to create Form D Display',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $display = FormDDisplay::with(['machine'])->findOrFail($id);

            return response()->json([
                'message' => 'Form D Display retrieved successfully',
                'data' => $display
            ]);
        } catch (\Exception $e) {
            \Log::error('Error retrieving Form D Display: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to retrieve Form D Display',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Log the incoming request for debugging
        \Log::info('Form D Display Update Request:', $request->all());

        $display = FormDDisplay::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'machine_id' => 'nullable|exists:master_machines,id_machine',
            'material_type' => 'nullable|string|max:10',
            'setting_march' => 'nullable|string',
            'shift' => 'nullable|string|max:2',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();

        try {
            $updateData = [];

            if ($request->has('machine_id')) {
                $updateData['machine_id'] = $request->machine_id;
            }

            if ($request->has('material_type')) {
                $updateData['material_type'] = $request->material_type;
            }

            if ($request->has('setting_march')) {
                $updateData['setting_march'] = $request->setting_march;
            }

            if ($request->has('shift')) {
                $updateData['shift'] = $request->shift;
            }

            $display->update($updateData);

            // Log the update
            Log::create([
                'action' => 'UPDATE FORM D DISPLAY',
                'created_date' => now(),
                'created_by' => $request->user() ? $request->user()->nik : 'system',
                'created_at' => now(),
                'details' => json_encode([
                    'display_id' => $display->id_form_d_display,
                ]),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Form D Display updated successfully',
                'data' => $display
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating Form D Display: ' . $e->getMessage());
            \Log::error('Error trace: ' . $e->getTraceAsString());

            return response()->json([
                'message' => 'Failed to update Form D Display',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id, Request $request)
    {
        DB::beginTransaction();

        try {
            $display = FormDDisplay::findOrFail($id);
            $display->delete();

            // Log the deletion
            Log::create([
                'action' => 'DELETE FORM D DISPLAY',
                'created_date' => now(),
                'created_by' => $request->user() ? $request->user()->nik : 'system',
                'created_at' => now(),
                'details' => json_encode([
                    'display_id' => $id,
                ]),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Form D Display deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting Form D Display: ' . $e->getMessage());
            \Log::error('Error trace: ' . $e->getTraceAsString());

            return response()->json([
                'message' => 'Failed to delete Form D Display',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get machine display data with related machine-specific display data
     */
    public function getMachineDisplayData($machineId)
    {
        try {
            // Get the machine
            $machine = MasterMachine::where('id_machine', $machineId)->first();
            
            if (!$machine) {
                return response()->json([
                    'message' => 'Machine not found in master_machines table',
                    'data' => null
                ], 404);
            }
            
            $display = FormDDisplay::where('machine_id', $machineId)->first();
            
            if (!$display) {
                return response()->json([
                    'message' => 'No display configuration found for this machine',
                    'data' => null
                ], 404);
            }
            
            // Check all display machine tables for this machine ID
            $result = [
                'message' => 'Machine display data retrieved successfully',
                'display_config' => $display,
                'machine_info' => $machine,
                'machine_data' => []
            ];
            
            // Check Assy table
            $assyData = DisplayMachineAssy::where('machine_id', $machineId)->get();
            if ($assyData->count() > 0) {
                $result['machine_data']['assy'] = $assyData;
            }
            
            // Check FCS table
            $fcsData = DisplayMachineFcs::where('machine_id', $machineId)->get();
            if ($fcsData->count() > 0) {
                $result['machine_data']['fcs'] = $fcsData;
            }
            
            // Check FCS SHI table
            $fcsShiData = DisplayMachineFcsShi::where('machine_id', $machineId)->get();
            if ($fcsShiData->count() > 0) {
                $result['machine_data']['fcs_shi'] = $fcsShiData;
            }
            
            // Check SHI-1 table
            $shi1Data = DisplayMachineShi1::where('machine_id', $machineId)->get();
            if ($shi1Data->count() > 0) {
                $result['machine_data']['shi_1'] = $shi1Data;
            }
            
            // Check SHI-2 table
            $shi2Data = DisplayMachineShi2::where('machine_id', $machineId)->get();
            if ($shi2Data->count() > 0) {
                $result['machine_data']['shi_2'] = $shi2Data;
            }
            
            // If no machine data found in any table
            if (empty($result['machine_data'])) {
                return response()->json([
                    'message' => 'No display machine data found for this machine',
                    'display_config' => $display,
                    'machine_info' => $machine,
                    'machine_data' => null
                ]);
            }
            
            return response()->json($result);
        } catch (\Exception $e) {
            \Log::error('Error retrieving machine display data: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to retrieve machine display data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get machine display data by BRM number
     */
    public function getDisplayDataByBrm($brmNo)
    {
        try {
            // Try to find display data in each machine-specific table
            $assyData = DisplayMachineAssy::where('brm_no', $brmNo)->with(['formDDisplay', 'machine'])->first();
            if ($assyData) {
                return response()->json([
                    'message' => 'Machine display data retrieved successfully',
                    'machine_type' => 'assy',
                    'data' => $assyData
                ]);
            }
            
            $fcsData = DisplayMachineFcs::where('brm_no', $brmNo)->with(['formDDisplay', 'machine'])->first();
            if ($fcsData) {
                return response()->json([
                    'message' => 'Machine display data retrieved successfully',
                    'machine_type' => 'fcs',
                    'data' => $fcsData
                ]);
            }
            
            $fcsShiData = DisplayMachineFcsShi::where('brm_no', $brmNo)->with(['formDDisplay', 'machine'])->first();
            if ($fcsShiData) {
                return response()->json([
                    'message' => 'Machine display data retrieved successfully',
                    'machine_type' => 'fcs_shi',
                    'data' => $fcsShiData
                ]);
            }
            
            $shi1Data = DisplayMachineShi1::where('brm_no', $brmNo)->with(['formDDisplay', 'machine'])->first();
            if ($shi1Data) {
                return response()->json([
                    'message' => 'Machine display data retrieved successfully',
                    'machine_type' => 'shi_1',
                    'data' => $shi1Data
                ]);
            }
            
            $shi2Data = DisplayMachineShi2::where('brm_no', $brmNo)->with(['formDDisplay', 'machine'])->first();
            if ($shi2Data) {
                return response()->json([
                    'message' => 'Machine display data retrieved successfully',
                    'machine_type' => 'shi_2',
                    'data' => $shi2Data
                ]);
            }
            
            return response()->json([
                'message' => 'No display data found for this BRM number',
                'data' => null
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Error retrieving display data by BRM: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to retrieve display data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
