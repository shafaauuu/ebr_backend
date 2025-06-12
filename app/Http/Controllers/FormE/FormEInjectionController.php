<?php

namespace App\Http\Controllers\FormE;

use App\Http\Controllers\Controller;
use App\Models\FormE\FormEInjection;
use App\Models\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FormEInjectionController extends Controller
{
    public function index(): JsonResponse
    {
        $forms = FormEInjection::all();
        return response()->json($forms);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'task_id'        => 'required|integer',
            'code_task'      => 'required|string',
            'jml_teoritis'   => 'nullable|integer',
            'jml_release'    => 'nullable|integer',
            'jml_karantina'  => 'nullable|integer',
            'jml_reject'     => 'nullable|integer',
            'jml_sisa'       => 'nullable|integer',
            'sample_ipc'     => 'nullable|integer',
            'sample_qc'      => 'nullable|integer',
            'sample_release' => 'nullable|integer',
            'yield'          => 'nullable|numeric',
            'total_hasil'    => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $form = FormEInjection::updateOrCreate($validator->validated());
        Log::create(
            [
                'action' => 'ADD FORM E INJECTION',
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
            $form = FormEInjection::where('task_id', $id)->orderBy('id', 'desc')->first();

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
        $form = FormEInjection::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'task_id'        => 'required|integer',
            'code_task'      => 'required|string',
            'jml_teoritis'   => 'nullable|integer',
            'jml_release'    => 'nullable|integer',
            'jml_karantina'  => 'nullable|integer',
            'jml_reject'     => 'nullable|integer',
            'jml_sisa'       => 'nullable|integer',
            'sample_ipc'     => 'nullable|integer',
            'sample_qc'      => 'nullable|integer',
            'sample_release' => 'nullable|integer',
            'yield'          => 'nullable|numeric',
            'total_hasil'    => 'nullable|integer',
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
        $form = FormEInjection::findOrFail($id);
        $form->delete();

        return response()->json(['message' => 'Form deleted successfully']);
    }
}
