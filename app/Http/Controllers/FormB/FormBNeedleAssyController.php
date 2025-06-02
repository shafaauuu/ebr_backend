<?php

namespace App\Http\Controllers\FormB;

use App\Http\Controllers\Controller;
use App\Models\FormB\FormBAssySyringe;
use App\Models\FormB\FormBNeedleAssy;
use App\Models\Log;
use App\Models\MasterMachine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FormBNeedleAssyController extends Controller
{
    public function storeQualification(Request $request): JsonResponse
    {
        // Get the data from the request
        $data = $request->all();

        // Check if the data is an array
        if (!is_array($data)) {
            return response()->json([
                'message' => 'Invalid data format. Expected an array of objects.',
            ], 400);
        }

        $savedRecords = [];

        // Loop through each item in the data
        foreach ($data as $item) {
            // Validate the item
            $validator = Validator::make($item, [
                'task_id' => 'required|integer',
                'code_task' => 'required|string',   // 'code_task' directly from Flutter
                'machine_id' => 'required|string',
                'terkualifikasi' => 'required|boolean',
            ]);

            // If validation fails, return a validation error response
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Directly get code_task from Flutter and skip unnecessary manipulation
            $code_task = $item['code_task'];

            // Find the machine_id from master_machines using brm_machine_id (this can be based on code_task)
            $machine = MasterMachine::where('machine_code', $item['machine_id'])->first();

            if (!$machine) {
                return response()->json([
                    'message' => 'Machine not found for the given machine code.',
                ], 404);
            }

            // Create or update the record in form_b
            $record = FormBNeedleAssy::updateOrCreate(
                [
                    'code_task' => $code_task,        // directly from Flutter
                    'machine_id' => $item['machine_id'], // directly from Flutter
                ],
                [
                    'task_id' => $item['task_id'],  // <- Add this
                    'terkualifikasi' => $item['terkualifikasi'],
                ]
            );

            // Add the saved record to the result array
            $savedRecords[] = $record;

            Log::create(
                [
                    'action' => 'ADD FORM B NEEDLE ASSY',
                    'created_date' => now(),
                    'created_by' => $request->user()->nik,
                    'created_at' => now(),
                    'task_id' => $item['task_id'],
                ]
            );
        }

        // Return success response with saved data
        return response()->json([
            'message' => 'Data saved successfully',
            'data' => $savedRecords,
        ], 200);
    }

    public function show($id): JsonResponse
    {
        try {
            $form = FormBNeedleAssy::where('task_id', $id)->orderBy('id', 'desc')->first();

            if (!$form) {
                return response()->json(['message' => 'Form not found'], 404);
            }

            return response()->json(['data' => $form], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        // Find the form by ID
        $form = FormBNeedleAssy::findOrFail($id);

        // Manually validate the incoming data
        $validator = Validator::make($request->all(), [
            'task_id' => 'required|integer',
            'code_task' => 'required|string',   // 'code_task' directly from Flutter
            'machine_id' => 'required|string',
            'terkualifikasi' => 'required|boolean',
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422); // Unprocessable Entity
        }

        // If validation passes, update the form
        $form->update($validator->validated());

        // Return the updated form as JSON
        return response()->json($form);
    }

    public function destroy($id): JsonResponse
    {
        // Find the form by ID
        $form = FormBNeedleAssy::findOrFail($id);

        // Delete the form
        $form->delete();

        // Return a 204 No Content response
        return response()->json(null, 204);
    }

}
