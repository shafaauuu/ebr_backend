<?php

namespace App\Http\Controllers\FormD;

use App\Http\Controllers\Controller;
use App\Models\Bom;
use App\Models\FormD\MaterialRecon;
use App\Models\Log;
use App\Models\MasterMaterial;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MaterialReconController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $materials = MaterialRecon::with(['task', 'material', 'bom'])->get();
        return response()->json($materials);
    }

    /**
     * Get child materials for a specific task
     */
    public function getChildMaterials($taskId, $materialCode)
    {
        try {
            // Load the task with the masterBrm relationship
            $task = Task::with('masterBrm')->findOrFail($taskId);

            // Check if masterBrm is loaded
            if (!$task->masterBrm) {
                return response()->json(['error' => 'BRM not found for this task'], 404);
            }

            // Get all BOMs where material_code matches and load the childMaterial relationship
            $childMaterials = Bom::with('childMaterial')
                ->where('material_code', $materialCode)
                ->get()
                ->filter() // Remove any null values
                ->values(); // Reset array keys

            return response()->json($childMaterials);

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Error in getChildMaterials: ' . $e->getMessage());
            return response()->json([
                'error' => 'An error occurred while fetching child materials',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Log the incoming request for debugging
        \Log::info('Material Recon Request:', $request->all());

        $validator = Validator::make($request->all(), [
            'code_task' => 'required|string|max:255',
            'materials' => 'required|array|min:1',
            'materials.*.material_code' => 'required|string|max:10',
            'materials.*.material_uom' => 'required|string|max:3',
            'materials.*.jml_awal' => 'required|integer|min:0',
            'materials.*.jml_spbt' => 'nullable|integer|min:0',
            'materials.*.jml_reject' => 'nullable|integer|min:0',
            'materials.*.jml_pakai' => 'nullable|integer|min:0',
            'materials.*.jml_karantina' => 'nullable|integer|min:0',
            'materials.*.sisa' => 'nullable|integer|min:0',
            'materials.*.jml_musnah' => 'nullable|integer|min:0',
            'materials.*.jml_kembali' => 'nullable|integer|min:0',
            'materials.*.id_mat' => 'required|exists:master_materials,id_mat',
            'materials.*.id_bom' => 'nullable|exists:boms,id_bom',
            'task_id' => 'required|exists:tasks,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $createdRecords = [];
        DB::beginTransaction();

        try {
            // Extract the materials array
            $materials = $request->materials;

            // Handle both array formats: indexed array or object with numeric keys
            if (!is_array($materials)) {
                $materials = (array)$materials;
            }

            foreach ($materials as $material) {
                // Convert to array if it's an object
                if (is_object($material)) {
                    $material = (array)$material;
                }

                // Create a new array with the data
                $materialData = [
                    'code_task' => $request->code_task,
                    'material_code' => $material['material_code'],
                    'material_uom' => $material['material_uom'],
                    'jml_awal' => $material['jml_awal'],
                    'jml_spbt' => $material['jml_spbt'] ?? 0,
                    'jml_reject' => $material['jml_reject'] ?? 0,
                    'jml_pakai' => $material['jml_pakai'] ?? 0,
                    'jml_karantina' => $material['jml_karantina'] ?? 0,
                    'sisa' => $material['sisa'] ?? 0,
                    'jml_musnah' => $material['jml_musnah'] ?? 0,
                    'jml_kembali' => $material['jml_kembali'] ?? 0,
                    'id_task' => $request->task_id,
                    'id_mat' => $material['id_mat'],
                    'id_bom' => $material['id_bom'] ?? null,
                ];

                // Check if record already exists
                $existingRecord = MaterialRecon::where('id_task', $request->task_id)
                    ->where('id_mat', $material['id_mat'])
                    ->first();

                if ($existingRecord) {
                    // Update existing record
                    $existingRecord->update($materialData);
                    $createdRecords[] = $existingRecord;
                } else {
                    // Create new record
                    $form = MaterialRecon::create($materialData);
                    $createdRecords[] = $form;
                }

                // Log each material submission
                Log::create([
                    'action' => 'ADD MATERIAL RECONCILIATION',
                    'created_date' => now(),
                    'created_by' => $request->user() ? $request->user()->nik : 'system',
                    'created_at' => now(),
                    'task_id' => $request->task_id,
                    'details' => json_encode([
                        'material_id' => $material['id_mat'],
                        'material_code' => $material['material_code'],
                    ]),
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Material reconciliation submitted successfully',
                'data' => $createdRecords
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error submitting material reconciliation: ' . $e->getMessage());
            \Log::error('Error trace: ' . $e->getTraceAsString());

            return response()->json([
                'message' => 'Failed to submit material reconciliation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id, Request $request)
    {
        try {
            $isTaskId = $request->query('is_task_id', true);

            if ($isTaskId) {
                // Get all records for the task
                $materials = MaterialRecon::where('id_task', $id)
                    ->with(['material', 'task', 'bom'])
                    ->orderBy('id_mat_recon', 'desc')
                    ->get();

                if ($materials->isEmpty()) {
                    return response()->json([
                        'message' => 'No material reconciliation records found for this task',
                        'data' => null
                    ], 404);
                }

                return response()->json([
                    'message' => 'Material reconciliation records retrieved successfully',
                    'data' => $materials
                ]);
            } else {
                // Get a specific record by its ID
                $material = MaterialRecon::with(['material', 'task', 'bom'])->findOrFail($id);

                return response()->json([
                    'message' => 'Material reconciliation record retrieved successfully',
                    'data' => $material
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Error retrieving material reconciliation: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to retrieve material reconciliation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Find the material reconciliation record
        $material = MaterialRecon::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'material_code' => 'nullable|string|max:10',
            'material_uom' => 'nullable|string|max:3',
            'jml_awal' => 'nullable|integer|min:0',
            'jml_spbt' => 'nullable|integer|min:0',
            'jml_reject' => 'nullable|integer|min:0',
            'jml_pakai' => 'nullable|integer|min:0',
            'jml_karantina' => 'nullable|integer|min:0',
            'sisa' => 'nullable|integer|min:0',
            'jml_musnah' => 'nullable|integer|min:0',
            'jml_kembali' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Update only the fields that are provided
            $updateData = [];

            foreach ([
                'material_code', 'material_uom', 'jml_awal', 'jml_spbt', 
                'jml_reject', 'jml_pakai', 'jml_karantina', 'sisa', 
                'jml_musnah', 'jml_kembali'
            ] as $field) {
                if ($request->has($field)) {
                    $updateData[$field] = $request->$field;
                }
            }

            $material->update($updateData);

            // Log the update
            Log::create([
                'action' => 'UPDATE MATERIAL RECONCILIATION',
                'created_date' => now(),
                'created_by' => $request->user() ? $request->user()->nik : 'system',
                'created_at' => now(),
                'task_id' => $material->id_task,
                'details' => json_encode([
                    'material_id' => $material->id_mat,
                    'material_code' => $material->material_code,
                ]),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Material reconciliation updated successfully',
                'data' => $material
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating material reconciliation: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to update material reconciliation',
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
            $material = MaterialRecon::findOrFail($id);
            $taskId = $material->id_task;
            $materialId = $material->id_mat;
            $materialCode = $material->material_code;

            DB::beginTransaction();

            // Delete the material
            $material->delete();

            // Log the deletion
            Log::create([
                'action' => 'DELETE MATERIAL RECONCILIATION',
                'created_date' => now(),
                'created_by' => $request->user() ? $request->user()->nik : 'system',
                'created_at' => now(),
                'task_id' => $taskId,
                'details' => json_encode([
                    'material_id' => $materialId,
                    'material_code' => $materialCode,
                ]),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Material reconciliation deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting material reconciliation: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to delete material reconciliation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get materials by material ID for a specific task
     */
    public function getMaterialsByMatId($taskId, $matId)
    {
        try {
            $materials = MaterialRecon::where('id_task', $taskId)
                ->where('id_mat', $matId)
                ->with(['material', 'bom'])
                ->get();

            return response()->json($materials);
        } catch (\Exception $e) {
            \Log::error('Error in getMaterialsByMatId: ' . $e->getMessage());
            return response()->json([
                'error' => 'An error occurred while fetching materials',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
