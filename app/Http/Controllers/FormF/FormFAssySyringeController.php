<?php

namespace App\Http\Controllers\FormF;

use App\Http\Controllers\Controller;
use App\Models\FormF\FormFAssySyringe;
use App\Models\Log;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FormFAssySyringeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $forms = FormFAssySyringe::with(['task'])->get();
        return response()->json($forms);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Log the incoming request for debugging
        \Log::info('Form F Assy Syringe Request:', $request->all());

        $validator = Validator::make($request->all(), [
            'label_mesin' => 'required',
            'label_2' => 'required',
            'task_code' => 'required|string|max:255',
            'task_id' => 'required|exists:tasks,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        \DB::beginTransaction();

        try {
            $formData = [
                'task_code' => $request->task_code,
                'task_id' => $request->task_id,
            ];

            // Process label_mesin file
            if ($request->hasFile('label_mesin')) {
                $fileContent = file_get_contents($request->file('label_mesin')->getRealPath());
                $formData['label_mesin'] = 'data:' . $request->file('label_mesin')->getMimeType() . ';base64,' . base64_encode($fileContent);
            } elseif ($request->has('label_mesin') && is_string($request->label_mesin) && !empty($request->label_mesin)) {
                // Store the base64 string directly
                $formData['label_mesin'] = $request->label_mesin;
            }
            
            // Process label_2 file
            if ($request->hasFile('label_2')) {
                $fileContent = file_get_contents($request->file('label_2')->getRealPath());
                $formData['label_2'] = 'data:' . $request->file('label_2')->getMimeType() . ';base64,' . base64_encode($fileContent);
            } elseif ($request->has('label_2') && is_string($request->label_2) && !empty($request->label_2)) {
                // Store the base64 string directly
                $formData['label_2'] = $request->label_2;
            }

            $form = FormFAssySyringe::create($formData);

            // Log the submission
            Log::create([
                'action' => 'ADD FORM F ASSY SYRINGE',
                'created_date' => now(),
                'created_by' => $request->user() ? $request->user()->nik : 'system',
                'created_at' => now(),
                'task_id' => $request->task_id,
                'details' => json_encode([
                    'form_id' => $form->id,
                ]),
            ]);

            \DB::commit();

            return response()->json([
                'message' => 'Form submitted successfully',
                'data' => $form
            ], 201);
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error submitting form F: ' . $e->getMessage());
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
    public function show($id, Request $request)
    {
        try {
            $isTaskId = $request->query('is_task_id', true);

            if ($isTaskId) {
                // Get all records for the task
                $forms = FormFAssySyringe::where('task_id', $id)
                    ->with(['task'])
                    ->orderBy('id', 'desc')
                    ->get();

                if ($forms->isEmpty()) {
                    return response()->json([
                        'message' => 'No Form F Assy Syringe records found for this task',
                        'data' => null
                    ], 404);
                }

                return response()->json([
                    'message' => 'Form F Assy Syringe records retrieved successfully',
                    'data' => $forms
                ]);
            } else {
                // Get a specific form by its ID
                $form = FormFAssySyringe::with(['task'])->findOrFail($id);

                return response()->json([
                    'message' => 'Form F Assy Syringe record retrieved successfully',
                    'data' => $form
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Error retrieving Form F: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to retrieve form',
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
        \Log::info('Form F Assy Syringe Update Request:', $request->all());

        $form = FormFAssySyringe::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'label_mesin' => 'nullable',
            'label_2' => 'nullable',
            'task_code' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        \DB::beginTransaction();

        try {
            $updateData = [];

            // Process label_mesin file if provided
            if ($request->hasFile('label_mesin')) {
                $fileContent = file_get_contents($request->file('label_mesin')->getRealPath());
                $updateData['label_mesin'] = 'data:' . $request->file('label_mesin')->getMimeType() . ';base64,' . base64_encode($fileContent);
            } elseif ($request->has('label_mesin') && is_string($request->label_mesin) && !empty($request->label_mesin)) {
                // Store the base64 string directly
                $updateData['label_mesin'] = $request->label_mesin;
            }
            
            // Process label_2 file if provided
            if ($request->hasFile('label_2')) {
                $fileContent = file_get_contents($request->file('label_2')->getRealPath());
                $updateData['label_2'] = 'data:' . $request->file('label_2')->getMimeType() . ';base64,' . base64_encode($fileContent);
            } elseif ($request->has('label_2') && is_string($request->label_2) && !empty($request->label_2)) {
                // Store the base64 string directly
                $updateData['label_2'] = $request->label_2;
            }

            // Update task_code if provided
            if ($request->has('task_code')) {
                $updateData['task_code'] = $request->task_code;
            }

            $form->update($updateData);

            // Log the update
            Log::create([
                'action' => 'UPDATE FORM F ASSY SYRINGE',
                'created_date' => now(),
                'created_by' => $request->user() ? $request->user()->nik : 'system',
                'created_at' => now(),
                'task_id' => $form->task_id,
                'details' => json_encode([
                    'form_id' => $form->id,
                ]),
            ]);

            \DB::commit();

            return response()->json([
                'message' => 'Form updated successfully',
                'data' => $form
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error updating form F: ' . $e->getMessage());
            \Log::error('Error trace: ' . $e->getTraceAsString());

            return response()->json([
                'message' => 'Failed to update form',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $form = FormFAssySyringe::findOrFail($id);
            $taskId = $form->task_id;
            
            $form->delete();

            // Log the deletion
            Log::create([
                'action' => 'DELETE FORM F ASSY SYRINGE',
                'created_date' => now(),
                'created_by' => request()->user() ? request()->user()->nik : 'system',
                'created_at' => now(),
                'task_id' => $taskId,
                'details' => json_encode([
                    'form_id' => $id,
                ]),
            ]);

            return response()->json([
                'message' => 'Form deleted successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting Form F: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to delete form',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
