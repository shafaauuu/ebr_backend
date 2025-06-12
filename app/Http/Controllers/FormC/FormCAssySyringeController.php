<?php

namespace App\Http\Controllers\FormC;

use App\Http\Controllers\Controller;
use App\Models\Bom;
use App\Models\FormC\FormCAssySyringe;
use App\Models\FormE\FormEAssySyringe;
use App\Models\Log;
use App\Models\MasterMaterial;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FormCAssySyringeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $forms = FormCAssySyringe::with(['task', 'material', 'brm'])->get();
        return response()->json($forms);
    }

    /**
     * Get child materials for a specific task
     */
    public function getChildMaterials($taskId)
    {
        try {
            // Load the task with the masterBrm relationship (note the method name matches the relationship in Task model)
            $task = Task::with('masterBrm')->findOrFail($taskId);

            // Check if masterBrm is loaded and has material_code
            if (!$task->masterBrm) {
                return response()->json(['error' => 'BRM not found for this task'], 404);
            }

            $materialCode = $task->masterBrm->material_code;

            // Get all BOMs where material_code matches and load the childMaterial relationship
            $childMaterials = Bom::with('childMaterial')
                ->where('material_code', $materialCode)
                ->get()
                ->pluck('childMaterial')
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
        \Log::info('Form C Assy Syringe Request:', $request->all());
        
        $validator = Validator::make($request->all(), [
            'code_task' => 'required|string|max:255',
            'id_brm' => 'required|string|max:255',
            'materials' => 'required|array|min:1',
            'materials.*.batch_no' => 'nullable|string|max:255',
            'materials.*.actual_qty' => 'required|integer|min:0',
            'materials.*.id_mat' => 'nullable',
            'sesuai_picklist' => 'required|boolean',
            'remarks_picklist' => 'nullable|string|max:255',
            'sesuai_bets' => 'required|boolean',
            'remarks_bets' => 'nullable|string|max:255',
            'mat_lengkap' => 'required|boolean',
            'remarks_mat' => 'nullable|string|max:255',
            'task_id' => 'required|exists:tasks,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Add custom validation for batch_no when actual_qty > 0
        foreach ($request->materials as $index => $material) {
            if (isset($material['actual_qty']) && $material['actual_qty'] > 0) {
                if (empty($material['batch_no'])) {
                    return response()->json([
                        'message' => 'Validation failed',
                        'errors' => [
                            "materials.{$index}.batch_no" => ['Batch number is required when quantity is greater than 0']
                        ]
                    ], 422);
                }
            }
        }

        $createdRecords = [];
        \DB::beginTransaction();

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
                
                // Create a new array with the common data
                $formData = [
                    'code_task' => $request->code_task,
                    'id_brm' => $request->id_brm,
                    'sesuai_picklist' => $request->sesuai_picklist,
                    'remarks_picklist' => $request->remarks_picklist ?? '',
                    'sesuai_bets' => $request->sesuai_bets,
                    'remarks_bets' => $request->remarks_bets ?? '',
                    'mat_lengkap' => $request->mat_lengkap,
                    'remarks_mat' => $request->remarks_mat ?? '',
                    'task_id' => $request->task_id,
                    'batch_no' => $material['batch_no'],
                    'actual_qty' => $material['actual_qty'],
                    'id_mat' => $material['id_mat'] ?? null,
                ];

                $form = FormCAssySyringe::create($formData);
                $createdRecords[] = $form;

                // Log each material submission
                Log::create([
                    'action' => 'ADD FORM C ASSY SYRINGE MATERIAL',
                    'created_date' => now(),
                    'created_by' => $request->user() ? $request->user()->nik : 'system',
                    'created_at' => now(),
                    'task_id' => $request->task_id,
                    'details' => json_encode([
                        'material_id' => $material['id_mat'] ?? null,
                        'batch_no' => $material['batch_no'],
                    ]),
                ]);
            }

            \DB::commit();

            return response()->json([
                'message' => 'Form submitted successfully',
                'data' => $createdRecords
            ], 201);
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error submitting form C: ' . $e->getMessage());
            \Log::error('Error trace: ' . $e->getTraceAsString());

            return response()->json([
                'message' => 'Failed to submit form',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Get all records for the task instead of just the latest one
        $forms = FormCAssySyringe::where('task_id', $id)
            ->with(['material'])
            ->orderBy('id', 'desc')
            ->get();
        
        // Group the forms by material ID for easier frontend processing
        $groupedForms = $forms->groupBy('id_mat')->map(function($items) {
            return $items->map(function($item) {
                return [
                    'id' => $item->id,
                    'batch_no' => $item->batch_no,
                    'actual_qty' => $item->actual_qty,
                    'material' => $item->material,
                    'created_at' => $item->created_at
                ];
            });
        });
        
        // Get the common data from the first record
        $commonData = null;
        if ($forms->isNotEmpty()) {
            $firstForm = $forms->first();
            $commonData = [
                'code_task' => $firstForm->code_task,
                'id_brm' => $firstForm->id_brm,
                'sesuai_picklist' => $firstForm->sesuai_picklist,
                'remarks_picklist' => $firstForm->remarks_picklist,
                'sesuai_bets' => $firstForm->sesuai_bets,
                'remarks_bets' => $firstForm->remarks_bets,
                'mat_lengkap' => $firstForm->mat_lengkap,
                'remarks_mat' => $firstForm->remarks_mat,
                'task_id' => $firstForm->task_id
            ];
        }
        
        return response()->json([
            'common_data' => $commonData,
            'materials' => $groupedForms
        ]);
    }

    /**
     * Get materials by mat_id for a specific task
     */
    public function getMaterialsByMatId($taskId, $matId)
    {
        $materials = FormCAssySyringe::where('task_id', $taskId)
            ->where('id_mat', $matId)
            ->with(['material'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json($materials);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'code_task' => 'sometimes|required|string|max:255',
            'id_brm' => 'sometimes|required|string|max:255',
            'materials' => 'sometimes|required|array|min:1',
            'materials.*.id' => 'sometimes|exists:form_c_assy_syringe,id',
            'materials.*.batch_no' => 'nullable|string|max:255',
            'materials.*.actual_qty' => 'required|integer|min:0',
            'materials.*.id_mat' => 'required|exists:master_materials,id_mat',
            'sesuai_picklist' => 'sometimes|required|boolean',
            'remarks_picklist' => 'nullable|string|max:255',
            'sesuai_bets' => 'sometimes|required|boolean',
            'remarks_bets' => 'nullable|string|max:255',
            'mat_lengkap' => 'sometimes|required|boolean',
            'remarks_mat' => 'nullable|string|max:255',
            'task_id' => 'sometimes|required|exists:tasks,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Add custom validation for batch_no when actual_qty > 0
        foreach ($request->materials as $index => $material) {
            if (isset($material['actual_qty']) && $material['actual_qty'] > 0) {
                if (empty($material['batch_no'])) {
                    return response()->json([
                        'message' => 'Validation failed',
                        'errors' => [
                            "materials.{$index}.batch_no" => ['Batch number is required when quantity is greater than 0']
                        ]
                    ], 422);
                }
            }
        }

        \DB::beginTransaction();

        try {
            // Find the task record
            $taskRecord = FormCAssySyringe::where('task_id', $id)->first();
            
            if (!$taskRecord) {
                return response()->json(['message' => 'Task not found'], 404);
            }
            
            // Update common fields
            $commonFields = [
                'code_task', 'id_brm', 'sesuai_picklist', 'remarks_picklist',
                'sesuai_bets', 'remarks_bets', 'mat_lengkap', 'remarks_mat', 'task_id'
            ];
            
            $updateData = array_intersect_key($request->all(), array_flip($commonFields));
            
            if (!empty($updateData)) {
                // Update all records for this task with common data
                FormCAssySyringe::where('task_id', $id)->update($updateData);
            }
            
            // Handle materials updates if present
            $updatedRecords = [];
            if ($request->has('materials')) {
                foreach ($request->materials as $material) {
                    if (isset($material['id'])) {
                        // Update existing material record
                        $materialRecord = FormCAssySyringe::findOrFail($material['id']);
                        $materialRecord->update([
                            'batch_no' => $material['batch_no'],
                            'actual_qty' => $material['actual_qty'],
                            'id_mat' => $material['id_mat']
                        ]);
                        $updatedRecords[] = $materialRecord;
                    } else {
                        // Create new material record for this task
                        $newRecord = FormCAssySyringe::create([
                            'code_task' => $taskRecord->code_task,
                            'id_brm' => $taskRecord->id_brm,
                            'batch_no' => $material['batch_no'],
                            'actual_qty' => $material['actual_qty'],
                            'id_mat' => $material['id_mat'],
                            'sesuai_picklist' => $taskRecord->sesuai_picklist,
                            'remarks_picklist' => $taskRecord->remarks_picklist,
                            'sesuai_bets' => $taskRecord->sesuai_bets,
                            'remarks_bets' => $taskRecord->remarks_bets,
                            'mat_lengkap' => $taskRecord->mat_lengkap,
                            'remarks_mat' => $taskRecord->remarks_mat,
                            'task_id' => $id
                        ]);
                        $updatedRecords[] = $newRecord;
                        
                        // Log the new material addition
                        Log::create([
                            'action' => 'UPDATE FORM C ASSY SYRINGE - ADD MATERIAL',
                            'created_date' => now(),
                            'created_by' => $request->user()->nik,
                            'created_at' => now(),
                            'task_id' => $id,
                            'details' => json_encode([
                                'material_id' => $material['id_mat'],
                                'batch_no' => $material['batch_no'],
                            ]),
                        ]);
                    }
                }
            }
            
            \DB::commit();
            
            return response()->json([
                'message' => 'Form updated successfully',
                'data' => $updatedRecords
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error updating form C: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Failed to update form',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a specific material entry from a task
     */
    public function deleteMaterial($id)
    {
        try {
            $material = FormCAssySyringe::findOrFail($id);
            $taskId = $material->task_id;
            $materialId = $material->id_mat;
            $batchNo = $material->batch_no;
            
            $material->delete();
            
            // Log the deletion
            Log::create([
                'action' => 'DELETE FORM C ASSY SYRINGE MATERIAL',
                'created_date' => now(),
                'created_by' => request()->user()->nik,
                'created_at' => now(),
                'task_id' => $taskId,
                'details' => json_encode([
                    'material_id' => $materialId,
                    'batch_no' => $batchNo,
                ]),
            ]);
            
            return response()->json([
                'message' => 'Material entry deleted successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting form C material: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Failed to delete material entry',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // In your Laravel controller
    public function getMaterialsByCodes(Request $request)
    {
        $codes = explode(',', $request->query('codes'));

        $materials = MasterMaterial::whereIn('material_code', $codes)
            ->get()
            ->map(function($material) {
                return [
                    'id' => $material->id,
                    'material_code' => $material->material_code,
                    'material_name' => $material->material_name,
                    // Add other fields you need
                ];
            });

        return response()->json($materials);
    }
}
