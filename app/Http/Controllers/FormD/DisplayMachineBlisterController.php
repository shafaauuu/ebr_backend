<?php

namespace App\Http\Controllers\FormD;

use App\Http\Controllers\Controller;
use App\Models\FormD\DisplayMachineBlister;
use App\Models\FormD\FormDDisplay;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DisplayMachineBlisterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $displays = DisplayMachineBlister::with(['formDDisplay', 'machine'])->get();
        return response()->json($displays);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Log the incoming request for debugging
        \Log::info('Display Machine Blister Request:', $request->all());

        $validator = Validator::make($request->all(), [
            'brm_no' => 'required|string|max:50',
            'machine_id' => 'required|exists:form_d_display,machine_id',
            'material_type' => 'required|string|max:100',
            'forming_time' => 'required|string|max:50',
            'forming_temperature' => 'required|string|max:50',
            'forming_pressure' => 'required|string|max:50',
            'sealing_temperature' => 'required|string|max:50',
            'sealing_pressure' => 'required|string|max:50',
            'sealing_time' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Check if entry already exists for this machine_id and brm_no
            $existingEntry = DisplayMachineBlister::where('machine_id', $request->machine_id)
                ->where('brm_no', $request->brm_no)
                ->first();
                
            if ($existingEntry) {
                // Update existing entry
                $existingEntry->update($request->all());
                $display = $existingEntry;
            } else {
                // Create new entry
                $display = DisplayMachineBlister::create($request->all());
            }

            // Log the submission
            Log::create([
                'action' => 'ADD/UPDATE DISPLAY MACHINE BLISTER',
                'created_date' => now(),
                'created_by' => $request->user() ? $request->user()->nik : 'system',
                'created_at' => now(),
                'details' => json_encode([
                    'brm_no' => $request->brm_no,
                    'machine_id' => $request->machine_id,
                ]),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Display Machine Blister data saved successfully',
                'data' => $display
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error saving Display Machine Blister: ' . $e->getMessage());
            \Log::error('Error trace: ' . $e->getTraceAsString());

            return response()->json([
                'message' => 'Failed to save Display Machine Blister data',
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
            // Check if ID is a BRM number
            if (is_string($id) && !is_numeric($id)) {
                $display = DisplayMachineBlister::where('brm_no', $id)
                    ->with(['formDDisplay', 'machine'])
                    ->first();
                
                if (!$display) {
                    return response()->json([
                        'message' => 'No Display Machine Blister found for this BRM',
                        'data' => null
                    ], 404);
                }
            } else {
                // Assume it's a machine_id
                $display = DisplayMachineBlister::where('machine_id', $id)
                    ->with(['formDDisplay', 'machine'])
                    ->first();
                
                if (!$display) {
                    return response()->json([
                        'message' => 'No Display Machine Blister found for this machine',
                        'data' => null
                    ], 404);
                }
            }

            return response()->json([
                'message' => 'Display Machine Blister retrieved successfully',
                'data' => $display
            ]);
        } catch (\Exception $e) {
            \Log::error('Error retrieving Display Machine Blister: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to retrieve Display Machine Blister',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $machineId, $brmNo)
    {
        // Log the incoming request for debugging
        \Log::info('Display Machine Blister Update Request:', $request->all());

        $display = DisplayMachineBlister::where('machine_id', $machineId)
            ->where('brm_no', $brmNo)
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'material_type' => 'nullable|string|max:100',
            'forming_time' => 'nullable|string|max:50',
            'forming_temperature' => 'nullable|string|max:50',
            'forming_pressure' => 'nullable|string|max:50',
            'sealing_temperature' => 'nullable|string|max:50',
            'sealing_pressure' => 'nullable|string|max:50',
            'sealing_time' => 'nullable|string|max:50',
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

            if ($request->has('material_type')) {
                $updateData['material_type'] = $request->material_type;
            }

            if ($request->has('forming_time')) {
                $updateData['forming_time'] = $request->forming_time;
            }

            if ($request->has('forming_temperature')) {
                $updateData['forming_temperature'] = $request->forming_temperature;
            }

            if ($request->has('forming_pressure')) {
                $updateData['forming_pressure'] = $request->forming_pressure;
            }

            if ($request->has('sealing_temperature')) {
                $updateData['sealing_temperature'] = $request->sealing_temperature;
            }

            if ($request->has('sealing_pressure')) {
                $updateData['sealing_pressure'] = $request->sealing_pressure;
            }

            if ($request->has('sealing_time')) {
                $updateData['sealing_time'] = $request->sealing_time;
            }

            $display->update($updateData);

            // Log the update
            Log::create([
                'action' => 'UPDATE DISPLAY MACHINE BLISTER',
                'created_date' => now(),
                'created_by' => $request->user() ? $request->user()->nik : 'system',
                'created_at' => now(),
                'details' => json_encode([
                    'machine_id' => $machineId,
                    'brm_no' => $brmNo,
                ]),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Display Machine Blister updated successfully',
                'data' => $display
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating Display Machine Blister: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to update Display Machine Blister',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($machineId, $brmNo)
    {
        try {
            $display = DisplayMachineBlister::where('machine_id', $machineId)
                ->where('brm_no', $brmNo)
                ->firstOrFail();

            $display->delete();

            return response()->json([
                'message' => 'Display Machine Blister deleted successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting Display Machine Blister: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to delete Display Machine Blister',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get display data by BRM number
     */
    public function getByBrm($brmNo)
    {
        try {
            $display = DisplayMachineBlister::where('brm_no', $brmNo)
                ->with(['formDDisplay', 'machine'])
                ->first();

            if (!$display) {
                return response()->json([
                    'message' => 'No Display Machine Blister found for this BRM',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'message' => 'Display Machine Blister retrieved successfully',
                'data' => $display
            ]);
        } catch (\Exception $e) {
            \Log::error('Error retrieving Display Machine Blister by BRM: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to retrieve Display Machine Blister by BRM',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
