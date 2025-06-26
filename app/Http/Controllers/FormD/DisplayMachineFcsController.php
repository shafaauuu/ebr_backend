<?php

namespace App\Http\Controllers\FormD;

use App\Http\Controllers\Controller;
use App\Models\FormD\DisplayMachineFcs;
use App\Models\FormD\FormDDisplay;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DisplayMachineFcsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $displays = DisplayMachineFcs::with(['formDDisplay', 'machine'])->get();
        return response()->json($displays);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Log the incoming request for debugging
        \Log::info('Display Machine FCS Request:', $request->all());

        $validator = Validator::make($request->all(), [
            'machine_id' => 'required|exists:form_d_display,machine_id',
            'material_type' => 'required|string|max:10',
            'brm_no' => 'required|string',
            // Add validation for other fields as needed
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
            $existingEntry = DisplayMachineFcs::where('machine_id', $request->machine_id)
                ->where('brm_no', $request->brm_no)
                ->first();
                
            if ($existingEntry) {
                // Update existing entry
                $existingEntry->update($request->all());
                $display = $existingEntry;
            } else {
                // Create new entry
                $display = DisplayMachineFcs::create($request->all());
            }

            // Log the submission
            Log::create([
                'action' => 'ADD/UPDATE DISPLAY MACHINE FCS',
                'created_date' => now(),
                'created_by' => $request->user() ? $request->user()->nik : 'system',
                'created_at' => now(),
                'details' => json_encode([
                    'id' => $display->id,
                    'brm_no' => $request->brm_no,
                    'machine_id' => $request->machine_id,
                ]),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Display Machine FCS data saved successfully',
                'data' => $display
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error saving Display Machine FCS: ' . $e->getMessage());
            \Log::error('Error trace: ' . $e->getTraceAsString());

            return response()->json([
                'message' => 'Failed to save Display Machine FCS data',
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
                $display = DisplayMachineFcs::where('brm_no', $id)
                    ->with(['formDDisplay', 'machine'])
                    ->first();
                
                if (!$display) {
                    return response()->json([
                        'message' => 'No Display Machine FCS found for this BRM',
                        'data' => null
                    ], 404);
                }
            } else {
                // If numeric, first try as ID
                if (is_numeric($id)) {
                    $display = DisplayMachineFcs::with(['formDDisplay', 'machine'])
                        ->find($id);
                    
                    if ($display) {
                        return response()->json([
                            'message' => 'Display Machine FCS retrieved successfully',
                            'data' => $display
                        ]);
                    }
                }
                
                // If not found by ID or not numeric, try as machine_id
                $display = DisplayMachineFcs::where('machine_id', $id)
                    ->with(['formDDisplay', 'machine'])
                    ->first();
                
                if (!$display) {
                    return response()->json([
                        'message' => 'No Display Machine FCS found for this machine',
                        'data' => null
                    ], 404);
                }
            }

            return response()->json([
                'message' => 'Display Machine FCS retrieved successfully',
                'data' => $display
            ]);
        } catch (\Exception $e) {
            \Log::error('Error retrieving Display Machine FCS: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to retrieve Display Machine FCS',
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
        \Log::info('Display Machine FCS Update Request:', $request->all());

        try {
            $display = DisplayMachineFcs::findOrFail($id);
            
            // Update the display with all provided fields
            $display->update($request->all());

            // Log the update
            Log::create([
                'action' => 'UPDATE DISPLAY MACHINE FCS',
                'created_date' => now(),
                'created_by' => $request->user() ? $request->user()->nik : 'system',
                'created_at' => now(),
                'details' => json_encode([
                    'id' => $id,
                    'machine_id' => $display->machine_id,
                    'brm_no' => $display->brm_no,
                ]),
            ]);

            return response()->json([
                'message' => 'Display Machine FCS updated successfully',
                'data' => $display
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating Display Machine FCS: ' . $e->getMessage());
            \Log::error('Error trace: ' . $e->getTraceAsString());

            return response()->json([
                'message' => 'Failed to update Display Machine FCS',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id, Request $request)
    {
        try {
            $display = DisplayMachineFcs::findOrFail($id);
            
            // Store info for logging before deletion
            $machineId = $display->machine_id;
            $brmNo = $display->brm_no;
            
            $display->delete();

            // Log the deletion
            Log::create([
                'action' => 'DELETE DISPLAY MACHINE FCS',
                'created_date' => now(),
                'created_by' => $request->user() ? $request->user()->nik : 'system',
                'created_at' => now(),
                'details' => json_encode([
                    'id' => $id,
                    'machine_id' => $machineId,
                    'brm_no' => $brmNo,
                ]),
            ]);

            return response()->json([
                'message' => 'Display Machine FCS deleted successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting Display Machine FCS: ' . $e->getMessage());
            \Log::error('Error trace: ' . $e->getTraceAsString());

            return response()->json([
                'message' => 'Failed to delete Display Machine FCS',
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
            $display = DisplayMachineFcs::where('brm_no', $brmNo)
                ->with(['formDDisplay', 'machine'])
                ->first();
            
            if (!$display) {
                return response()->json([
                    'message' => 'No Display Machine FCS found for this BRM',
                    'data' => null
                ], 404);
            }
            
            return response()->json([
                'message' => 'Display Machine FCS retrieved successfully',
                'data' => $display
            ]);
        } catch (\Exception $e) {
            \Log::error('Error retrieving Display Machine FCS by BRM: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to retrieve Display Machine FCS',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
