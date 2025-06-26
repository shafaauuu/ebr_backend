<?php

namespace App\Http\Controllers\FormD;

use App\Http\Controllers\Controller;
use App\Models\FormD\DisplayMachineSgp;
use App\Models\FormD\FormDDisplay;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DisplayMachineSgpController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $displays = DisplayMachineSgp::with(['formDDisplay', 'machine'])->get();
        return response()->json($displays);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Log the incoming request for debugging
        \Log::info('Display Machine SGP Request:', $request->all());

        $validator = Validator::make($request->all(), [
            'brm_no' => 'required|string|max:50',
            'machine_id' => 'required|exists:form_d_display,machine_id',
            'material_type' => 'required|string|max:100',
            'temp1' => 'required|string|max:255',
            'temp2' => 'required|string|max:255',
            'temp3' => 'required|string|max:255',
            'temp4' => 'required|string|max:255',
            'load_cap' => 'required|boolean',
            'load_hub' => 'required|boolean',
            'load_needle' => 'required|boolean',
            'hasil_epoxy' => 'required|boolean',
            'pressure_actual' => 'required|string|max:50',
            'pressure_status' => 'required|boolean',
            'low_epoxy1' => 'required|boolean',
            'low_epoxy2' => 'required|boolean',
            'low_epoxy3' => 'required|boolean',
            'hub_canula1' => 'required|boolean',
            'hub_canula2' => 'required|boolean',
            'hub_canula3' => 'required|boolean',
            'exc_epoxy1' => 'required|boolean',
            'exc_epoxy2' => 'required|boolean',
            'exc_epoxy3' => 'required|boolean',
            'needle_tumpul1' => 'required|boolean',
            'needle_tumpul2' => 'required|boolean',
            'needle_tumpul3' => 'required|boolean',
            'needle_balik1' => 'required|boolean',
            'needle_balik2' => 'required|boolean',
            'needle_balik3' => 'required|boolean',
            'needle_tersumbat1' => 'required|boolean',
            'needle_tersumbat2' => 'required|boolean',
            'needle_tersumbat3' => 'required|boolean',
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
            $existingEntry = DisplayMachineSgp::where('machine_id', $request->machine_id)
                ->where('brm_no', $request->brm_no)
                ->first();
                
            if ($existingEntry) {
                // Update existing entry
                $existingEntry->update($request->all());
                $display = $existingEntry;
            } else {
                // Create new entry
                $display = DisplayMachineSgp::create($request->all());
            }

            // Log the submission
            Log::create([
                'action' => 'ADD/UPDATE DISPLAY MACHINE SGP',
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
                'message' => 'Display Machine SGP data saved successfully',
                'data' => $display
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error saving Display Machine SGP: ' . $e->getMessage());
            \Log::error('Error trace: ' . $e->getTraceAsString());

            return response()->json([
                'message' => 'Failed to save Display Machine SGP data',
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
                $display = DisplayMachineSgp::where('brm_no', $id)
                    ->with(['formDDisplay', 'machine'])
                    ->first();
                
                if (!$display) {
                    return response()->json([
                        'message' => 'No Display Machine SGP found for this BRM',
                        'data' => null
                    ], 404);
                }
            } else {
                // Assume it's a machine_id
                $display = DisplayMachineSgp::where('machine_id', $id)
                    ->with(['formDDisplay', 'machine'])
                    ->first();
                
                if (!$display) {
                    return response()->json([
                        'message' => 'No Display Machine SGP found for this machine',
                        'data' => null
                    ], 404);
                }
            }

            return response()->json([
                'message' => 'Display Machine SGP retrieved successfully',
                'data' => $display
            ]);
        } catch (\Exception $e) {
            \Log::error('Error retrieving Display Machine SGP: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to retrieve Display Machine SGP',
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
        \Log::info('Display Machine SGP Update Request:', $request->all());

        $display = DisplayMachineSgp::where('machine_id', $machineId)
            ->where('brm_no', $brmNo)
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'material_type' => 'nullable|string|max:100',
            'temp1' => 'nullable|string|max:255',
            'temp2' => 'nullable|string|max:255',
            'temp3' => 'nullable|string|max:255',
            'temp4' => 'nullable|string|max:255',
            'load_cap' => 'nullable|boolean',
            'load_hub' => 'nullable|boolean',
            'load_needle' => 'nullable|boolean',
            'hasil_epoxy' => 'nullable|boolean',
            'pressure_actual' => 'nullable|string|max:50',
            'pressure_status' => 'nullable|boolean',
            'low_epoxy1' => 'nullable|boolean',
            'low_epoxy2' => 'nullable|boolean',
            'low_epoxy3' => 'nullable|boolean',
            'hub_canula1' => 'nullable|boolean',
            'hub_canula2' => 'nullable|boolean',
            'hub_canula3' => 'nullable|boolean',
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

            // Check each field and add to updateData if present in request
            foreach ($validator->getRules() as $field => $rules) {
                if ($request->has($field)) {
                    $updateData[$field] = $request->$field;
                }
            }

            $display->update($updateData);

            // Log the update
            Log::create([
                'action' => 'UPDATE DISPLAY MACHINE SGP',
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
                'message' => 'Display Machine SGP updated successfully',
                'data' => $display
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating Display Machine SGP: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to update Display Machine SGP',
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
            $display = DisplayMachineSgp::where('machine_id', $machineId)
                ->where('brm_no', $brmNo)
                ->firstOrFail();

            $display->delete();

            return response()->json([
                'message' => 'Display Machine SGP deleted successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting Display Machine SGP: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to delete Display Machine SGP',
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
            $display = DisplayMachineSgp::where('brm_no', $brmNo)
                ->with(['formDDisplay', 'machine'])
                ->first();

            if (!$display) {
                return response()->json([
                    'message' => 'No Display Machine SGP found for this BRM',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'message' => 'Display Machine SGP retrieved successfully',
                'data' => $display
            ]);
        } catch (\Exception $e) {
            \Log::error('Error retrieving Display Machine SGP by BRM: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to retrieve Display Machine SGP by BRM',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
