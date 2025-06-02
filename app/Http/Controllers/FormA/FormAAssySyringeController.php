<?php

namespace App\Http\Controllers\FormA;

use App\Http\Controllers\Controller;
use App\Models\FormA\FormAAssySyringe;
use App\Models\FormA\FormABlister;
use App\Models\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FormAAssySyringeController extends Controller
{
    public function index(): JsonResponse
    {
        $forms = FormAAssySyringe::all();
        return response()->json($forms);
    }

    // POST /form-a-assy-syringe
    public function store(Request $request): JsonResponse
    {
            $validator = Validator::make($request->all(), [
                'task_id' => 'required|integer',
                'code_task' => 'required|string',
                'tanggal' => 'required|date',
                'sebelum_produk' => 'required|string',
                'sebelum_bets' => 'required|string',
                'sebelum_needle' => 'required|string',
                'bersih_palet' => 'required|boolean',
                'bersih_lantai' => 'required|boolean',
                'bersih_kolong' => 'required|boolean',
                'bersih_mesin' => 'required|boolean',
                'bersih_grill' => 'required|boolean',
                'sisa_lantai' => 'required|boolean',
                'sisa_barrel' => 'required|boolean',
                'sisa_plunger' => 'required|boolean',
                'sisa_needle' => 'required|boolean',
                'sisa_gasket' => 'required|boolean',
                'sisa_starwhell' => 'required|boolean',
                'sisa_box' => 'required|boolean',
                'sisa_reject' => 'required|boolean',
                'sisa_rework' => 'required|boolean',
                'sebelum_dokumen' => 'required|boolean',
                'material_sesuai' => 'required|boolean',
                'saat_dokumen' => 'required|boolean',
                'suhu' => 'required|numeric',
                'kelembapan' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 422); // Unprocessable Entity
            }

            $form = FormAAssySyringe::updateOrCreate($validator->validated());
            Log::create(
                [
                    'action' => 'ADD FORM A ASSY SYRINGE',
                    'created_date' => now(),
                    'created_by' => $request->user()->nik,
                    'created_at' => now(),
                    'task_id' => $request->task_id,
                ]
            );

        return response()->json($form, 201);
    }

    public function show($id): JsonResponse
    {
        try {
            $form = FormAAssySyringe::where('task_id', $id)->orderBy('id', 'desc')->first();

            if (!$form) {
                return response()->json(['message' => 'Form not found'], 404);
            }

            return response()->json(['data' => $form], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // PUT /form-a-assy-syringe/{id}
    public function update(Request $request, $id): JsonResponse
    {
        // Find the form by ID
        $form = FormAAssySyringe::findOrFail($id);

        // Manually validate the incoming data
        $validator = Validator::make($request->all(), [
            'code_task' => 'required|string',
            'tanggal' => 'required|date',
            'sebelum_produk' => 'required|string',
            'sebelum_bets' => 'required|string',
            'sebelum_needle' => 'required|string',
            'bersih_palet' => 'required|boolean',
            'bersih_lantai' => 'required|boolean',
            'bersih_kolong' => 'required|boolean',
            'bersih_mesin' => 'required|boolean',
            'bersih_grill' => 'required|boolean',
            'sisa_lantai' => 'required|boolean',
            'sisa_barrel' => 'required|boolean',
            'sisa_plunger' => 'required|boolean',
            'sisa_needle' => 'required|boolean',
            'sisa_gasket' => 'required|boolean',
            'sisa_starwhell' => 'required|boolean',
            'sisa_box' => 'required|boolean',
            'sisa_reject' => 'required|boolean',
            'sisa_rework' => 'required|boolean',
            'sebelum_dokumen' => 'required|boolean',
            'material_sesuai' => 'required|boolean',
            'saat_dokumen' => 'required|boolean',
            'suhu' => 'required|numeric',
            'kelembapan' => 'required|numeric',
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
        $form = FormAAssySyringe::findOrFail($id);

        // Delete the form
        $form->delete();

        // Return a 204 No Content response
        return response()->json(null, 204);
    }

}
