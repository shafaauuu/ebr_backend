<?php

namespace App\Http\Controllers\FormG;

use App\Http\Controllers\Controller;
use App\Models\FormG\FormGInjection;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FormGInjectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $forms = FormGInjection::with(['task'])->get();

        $formattedForms = [];
        foreach ($forms as $form) {
            $formattedForms[] = $this->formatFormData($form);
        }

        return response()->json($formattedForms);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Log the incoming request for debugging
        \Log::info('Form G Bliste Request:', $request->except(['signed_1', 'signed_2', 'signed_3']));

        $validator = Validator::make($request->all(), [
            'remarks' => 'nullable|string|max:255',
            'signed_1' => 'nullable',
            'inisial_1' => 'nullable|string|max:3',
            'signed_2' => 'nullable',
            'inisial_2' => 'nullable|string|max:3',
            'signed_3' => 'nullable',
            'inisial_3' => 'nullable|string|max:3',
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
                'remarks' => $request->remarks,
                'inisial_1' => $request->inisial_1,
                'inisial_2' => $request->inisial_2,
                'inisial_3' => $request->inisial_3,
                'task_code' => $request->task_code,
                'task_id' => $request->task_id,
            ];

            // Process signed_1 data
            if ($request->hasFile('signed_1')) {
                $fileContent = file_get_contents($request->file('signed_1')->getRealPath());
                $formData['signed_1'] = 'data:image/png;base64,' . base64_encode($fileContent);
            } elseif ($request->has('signed_1') && is_string($request->signed_1) && !empty($request->signed_1)) {
                // Store the base64 string directly
                $formData['signed_1'] = $request->signed_1;
            }

            // Process signed_2 data
            if ($request->hasFile('signed_2')) {
                $fileContent = file_get_contents($request->file('signed_2')->getRealPath());
                $formData['signed_2'] = 'data:image/png;base64,' . base64_encode($fileContent);
            } elseif ($request->has('signed_2') && is_string($request->signed_2) && !empty($request->signed_2)) {
                // Store the base64 string directly
                $formData['signed_2'] = $request->signed_2;
            }

            // Process signed_3 data
            if ($request->hasFile('signed_3')) {
                $fileContent = file_get_contents($request->file('signed_3')->getRealPath());
                $formData['signed_3'] = 'data:image/png;base64,' . base64_encode($fileContent);
            } elseif ($request->has('signed_3') && is_string($request->signed_3) && !empty($request->signed_3)) {
                // Store the base64 string directly
                $formData['signed_3'] = $request->signed_3;
            }

            $form = FormGInjection::create($formData);

            // Log the submission
            Log::create([
                'action' => 'ADD FORM G INJECTION',
                'created_date' => now(),
                'created_by' => $request->user() ? $request->user()->nik : 'system',
                'created_at' => now(),
                'task_id' => $request->task_id,
                'details' => json_encode([
                    'form_id' => $form->id,
                ]),
            ]);

            \DB::commit();

            // Return a simplified response without binary data
            return response()->json([
                'message' => 'Form submitted successfully',
                'data' => [
                    'id' => $form->id,
                    'remarks' => $form->remarks,
                    'inisial_1' => $form->inisial_1,
                    'inisial_2' => $form->inisial_2,
                    'inisial_3' => $form->inisial_3,
                    'task_code' => $form->task_code,
                    'task_id' => $form->task_id,
                    'created_at' => $form->created_at,
                    'updated_at' => $form->updated_at,
                ]
            ], 201);
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error submitting form G: ' . $e->getMessage());
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
                $forms = FormGInjection::where('task_id', $id)
                    ->with(['task'])
                    ->orderBy('id', 'desc')
                    ->get();

                if ($forms->isEmpty()) {
                    return response()->json([
                        'message' => 'No Form G Injection records found for this task',
                        'data' => null
                    ], 404);
                }

                $formattedForms = [];
                foreach ($forms as $form) {
                    $formattedForms[] = $this->formatFormData($form);
                }

                return response()->json([
                    'message' => 'Form G Injection records retrieved successfully',
                    'data' => $formattedForms
                ]);
            } else {
                // Get a specific form by its ID
                $form = FormGInjection::with(['task'])->findOrFail($id);

                return response()->json([
                    'message' => 'Form G Injection record retrieved successfully',
                    'data' => $this->formatFormData($form)
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Error retrieving Form G: ' . $e->getMessage());
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
        \Log::info('Form G Injection Update Request:', $request->except(['signed_1', 'signed_2', 'signed_3']));

        $form = FormGInjection::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'remarks' => 'nullable|string|max:255',
            'signed_1' => 'nullable',
            'inisial_1' => 'nullable|string|max:3',
            'signed_2' => 'nullable',
            'inisial_2' => 'nullable|string|max:3',
            'signed_3' => 'nullable',
            'inisial_3' => 'nullable|string|max:3',
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

            // Update remarks if provided
            if ($request->has('remarks')) {
                $updateData['remarks'] = $request->remarks;
            }

            // Update initials if provided
            if ($request->has('inisial_1')) {
                $updateData['inisial_1'] = $request->inisial_1;
            }

            if ($request->has('inisial_2')) {
                $updateData['inisial_2'] = $request->inisial_2;
            }

            if ($request->has('inisial_3')) {
                $updateData['inisial_3'] = $request->inisial_3;
            }

            // Process signed_1 data
            if ($request->hasFile('signed_1')) {
                $fileContent = file_get_contents($request->file('signed_1')->getRealPath());
                $updateData['signed_1'] = 'data:image/png;base64,' . base64_encode($fileContent);
            } elseif ($request->has('signed_1') && is_string($request->signed_1) && !empty($request->signed_1)) {
                // Store the base64 string directly
                $updateData['signed_1'] = $request->signed_1;
            }

            // Process signed_2 data
            if ($request->hasFile('signed_2')) {
                $fileContent = file_get_contents($request->file('signed_2')->getRealPath());
                $updateData['signed_2'] = 'data:image/png;base64,' . base64_encode($fileContent);
            } elseif ($request->has('signed_2') && is_string($request->signed_2) && !empty($request->signed_2)) {
                // Store the base64 string directly
                $updateData['signed_2'] = $request->signed_2;
            }

            // Process signed_3 data
            if ($request->hasFile('signed_3')) {
                $fileContent = file_get_contents($request->file('signed_3')->getRealPath());
                $updateData['signed_3'] = 'data:image/png;base64,' . base64_encode($fileContent);
            } elseif ($request->has('signed_3') && is_string($request->signed_3) && !empty($request->signed_3)) {
                // Store the base64 string directly
                $updateData['signed_3'] = $request->signed_3;
            }

            // Update task_code if provided
            if ($request->has('task_code')) {
                $updateData['task_code'] = $request->task_code;
            }

            $form->update($updateData);

            // Log the update
            Log::create([
                'action' => 'UPDATE FORM G INJECTION',
                'created_date' => now(),
                'created_by' => $request->user() ? $request->user()->nik : 'system',
                'created_at' => now(),
                'task_id' => $form->task_id,
                'details' => json_encode([
                    'form_id' => $form->id,
                ]),
            ]);

            \DB::commit();

            // Return a simplified response without binary data
            return response()->json([
                'message' => 'Form updated successfully',
                'data' => [
                    'id' => $form->id,
                    'remarks' => $form->remarks,
                    'inisial_1' => $form->inisial_1,
                    'inisial_2' => $form->inisial_2,
                    'inisial_3' => $form->inisial_3,
                    'task_code' => $form->task_code,
                    'task_id' => $form->task_id,
                    'created_at' => $form->created_at,
                    'updated_at' => $form->updated_at,
                ]
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error updating form G: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to update form',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $form = FormGInjection::findOrFail($id);

        \DB::beginTransaction();

        try {
            // Log the deletion
            Log::create([
                'action' => 'DELETE FORM G INJECTION',
                'created_date' => now(),
                'created_by' => $request->user() ? $request->user()->nik : 'system',
                'created_at' => now(),
                'task_id' => $form->task_id,
                'details' => json_encode([
                    'form_id' => $id,
                ]),
            ]);

            $form->delete();

            \DB::commit();

            return response()->json([
                'message' => 'Form deleted successfully'
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error deleting form G: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to delete form',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Format form data for API responses
     * Handles signature data as strings
     */
    private function formatFormData($form)
    {
        $formattedData = [
            'id' => $form->id,
            'remarks' => $form->remarks,
            'inisial_1' => $form->inisial_1,
            'inisial_2' => $form->inisial_2,
            'inisial_3' => $form->inisial_3,
            'task_code' => $form->task_code,
            'task_id' => $form->task_id,
            'has_signed_1' => $form->has_signed_1,
            'has_signed_2' => $form->has_signed_2,
            'has_signed_3' => $form->has_signed_3,
            'created_at' => $form->created_at,
            'updated_at' => $form->updated_at,
            'task' => $form->task ? [
                'id' => $form->task->id,
                'code' => $form->task->code,
                'name' => $form->task->name,
                'status' => $form->task->status,
            ] : null,
        ];

        // Add signatures if they exist (already stored as strings)
        if (!empty($form->signed_1)) {
            $formattedData['signed_1'] = $form->signed_1;
        }

        if (!empty($form->signed_2)) {
            $formattedData['signed_2'] = $form->signed_2;
        }

        if (!empty($form->signed_3)) {
            $formattedData['signed_3'] = $form->signed_3;
        }

        return $formattedData;
    }
}
