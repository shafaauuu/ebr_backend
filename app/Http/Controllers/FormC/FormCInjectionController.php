<?php

namespace App\Http\Controllers\FormC;

use App\Http\Controllers\Controller;
use App\Models\Bom;
use App\Models\FormC\FormCInjection;
use App\Models\Log;
use App\Models\MasterMaterial;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class FormCInjectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $forms = FormCInjection::with(['task', 'material', 'brm'])->get();
        return response()->json($forms);
    }

    /**
     * Get child materials for a specific task
     */
    public function getChildMaterials($taskId)
    {
        try {
            // Load the task with the masterBrm relationship
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
        \Log::info('Form C Injection Request:', $request->all());
        
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

                $form = FormCInjection::create($formData);
                $createdRecords[] = $form;

                // Log each material submission
                Log::create([
                    'action' => 'ADD FORM C INJECTION MATERIAL',
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
        $forms = FormCInjection::where('task_id', $id)
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $form = FormCInjection::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'batch_no' => 'required|string|max:255',
            'actual_qty' => 'required|integer|min:1',
            'id_mat' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $form->update($validator->validated());
        
        return response()->json([
            'message' => 'Form updated successfully',
            'data' => $form
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $form = FormCInjection::findOrFail($id);
        $form->delete();
        
        return response()->json(['message' => 'Form deleted successfully']);
    }
}
