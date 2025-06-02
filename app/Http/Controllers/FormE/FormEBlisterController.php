<?php

namespace App\Http\Controllers\FormE;

use App\Http\Controllers\Controller;
use App\Models\FormA\FormABlister;
use App\Models\FormE\FormEBlister;
use App\Models\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FormEBlisterController extends Controller
{
    public function index(): JsonResponse
    {
        $forms = FormEBlister::all();
        return response()->json($forms);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'task_id' => 'required|integer',
            'code_task' => 'required|string|max:255',
            'jml_awal_assy' => 'required|integer',
            'jml_karantina_assy' => 'required|integer',
            'total_syringe' => 'required|integer',
            'jml_fg' => 'required|integer',
            'jml_karantina' => 'required|integer',
            'jml_reject' => 'required|integer',
            'jml_sisa' => 'required|integer',
            'sample_ipc' => 'required|integer',
            'sample_qc' => 'required|integer',
            'sample_released' => 'required|integer',
            'yield' => 'required|integer',
            'total_prod' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $form = FormEBlister::updateOrCreate($validator->validated());
        Log::create(
            [
                'action' => 'ADD FORM E BLISTER',
                'created_date' => now(),
                'created_by' => $request->user()->nik,
                'created_at' => now(),
                'task_id' => $request->task_id,
            ]
        );
        return response()->json([
            'message' => 'Form submitted successfully',
            'data' => $form
        ], 201);
    }

    public function show($id): JsonResponse
    {
        try{
            $form = FormEBlister::where('task_id', $id)->orderBy('id', 'desc')->first();

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
        $form = FormEBlister::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'task_id' => 'required|integer',
            'code_task' => 'required|string|max:255',
            'jml_awal_assy' => 'required|integer',
            'jml_karantina_assy' => 'required|integer',
            'total_syringe' => 'required|integer',
            'jml_fg' => 'required|integer',
            'jml_karantina' => 'required|integer',
            'jml_reject' => 'required|integer',
            'jml_sisa' => 'required|integer',
            'sample_ipc' => 'required|integer',
            'sample_qc' => 'required|integer',
            'sample_released' => 'required|integer',
            'yield' => 'required|integer',
            'total_prod' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $form->update($validator->validated());

        return response()->json($form);
    }

    public function destroy($id): JsonResponse
    {
        $form = FormEBlister::findOrFail($id);
        $form->delete();

        return response()->json(['message' => 'Form deleted successfully']);
    }
}

