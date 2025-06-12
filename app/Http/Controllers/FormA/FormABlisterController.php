<?php

namespace App\Http\Controllers\FormA;

use App\Http\Controllers\Controller;
use App\Models\FormA\FormABlister;
use App\Models\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FormABlisterController extends Controller
{
    // GET /form-a-blister
    public function index(): JsonResponse
    {
        $forms = FormABlister::all();
        return response()->json($forms);
    }

    // POST /form-a-blister
    public function store(Request $request): JsonResponse
    {
        // Manually validate the incoming data
        $validator = Validator::make($request->all(), [
            'task_id' => 'required|integer',
            'code_task' => 'required|string',
            'tanggal' => 'required|date',
            'sebelum_produk' => 'required|string',
            'sebelum_bets' => 'required|string',
            'bersih_lantai' => 'required|boolean',
            'bersih_dinding' => 'required|boolean',
            'bersih_grill' => 'required|boolean',
            'bersih_alat' => 'required|boolean',
            'sisa_produk' => 'required|boolean',
            'sebelum_dokumen' => 'required|boolean',
            'material_sesuai' => 'required|boolean',
            'saat_dokumen' => 'required|boolean',
            'suhu' => 'nullable|numeric',
            'kelembapan' => 'nullable|numeric',
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422); // Unprocessable Entity
        }

        // If validation passes, create or update the form
        $existing = FormABlister::where('task_id', $request->task_id)->first();
        $form = FormABlister::updateOrCreate(
            ['task_id' => $request->task_id],
            $validator->validated()
        );
        
        $action = $existing ? 'UPDATE FORM A BLISTER' : 'ADD FORM A BLISTER';
        Log::create([
            'action' => $action,
            'created_date' => now(),
            'created_by' => $request->user()->nik,
            'created_at' => now(),
            'task_id' => $request->task_id,
        ]);

        // Return the created form as JSON
        return response()->json($form, 201);
    }

    // GET /form-a-blister/{id}
    public function show($id): JsonResponse
    {
        try {
            $form = FormABlister::where('task_id', $id)->orderBy('id', 'desc')->first();

            if (!$form) {
                return response()->json(['message' => 'Form not found'], 404);
            }


            return response()->json(['data' => $form], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // PUT /form-a-blister/{id}
    public function update(Request $request, $id): JsonResponse
    {
        // Find the form by ID
        $form = FormABlister::findOrFail($id);

        // Manually validate the incoming data
        $validator = Validator::make($request->all(), [
            'code_task' => 'required|string',
            'tanggal' => 'required|date',
            'sebelum_produk' => 'required|string',
            'sebelum_bets' => 'required|string',
            'bersih_lantai' => 'required|boolean',
            'bersih_dinding' => 'required|boolean',
            'bersih_grill' => 'required|boolean',
            'bersih_alat' => 'required|boolean',
            'sisa_produk' => 'required|boolean',
            'sebelum_dokumen' => 'required|boolean',
            'material_sesuai' => 'required|boolean',
            'saat_dokumen' => 'required|boolean',
            'suhu' => 'nullable|numeric',
            'kelembapan' => 'nullable|numeric',
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

    // DELETE /form-a-blister/{id}
    public function destroy($id): JsonResponse
    {
        // Find the form by ID
        $form = FormABlister::findOrFail($id);

        // Delete the form
        $form->delete();

        // Return a 204 No Content response
        return response()->json(null, 204);
    }
}
