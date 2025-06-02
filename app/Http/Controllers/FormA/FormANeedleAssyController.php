<?php

namespace App\Http\Controllers\FormA;

use App\Http\Controllers\Controller;
use App\Models\FormA\FormANeedleAssy;
use App\Models\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FormANeedleAssyController extends Controller
{
    // Fetch all records
    public function index(): JsonResponse
    {
        $forms = FormANeedleAssy::all();
        return response()->json($forms);
    }

    // Store a new record
    public function store(Request $request): JsonResponse
    {
        // Manually validate the incoming data
        $validator = Validator::make($request->all(), [
            'task_id'          => 'required|integer',
            'code_task'        => 'required|string',
            'tanggal'          => 'required|date',
            'sebelum_produk'   => 'required|string',
            'sebelum_bets'     => 'required|string',
            'sebelum_needle'   => 'required|string',
            'sebelum_cap'      => 'required|string',
            'bersih_palet'     => 'required|boolean',
            'bersih_lantai'    => 'required|boolean',
            'bersih_kolong'    => 'required|boolean',
            'bersih_mesin'     => 'required|boolean',
            'bersih_grill'     => 'required|boolean',
            'sisa_lantai'      => 'required|boolean',
            'sisa_hub'         => 'required|boolean',
            'sisa_cap'         => 'required|boolean',
            'sisa_canula'      => 'required|boolean',
            'sisa_box'         => 'nullable|boolean',
            'sisa_reject'      => 'nullable|boolean',
            'sisa_rework'      => 'nullable|boolean',
            'sebelum_dokumen'  => 'required|boolean',
            'material_sesuai'  => 'required|boolean',
            'saat_dokumen'     => 'required|boolean',
            'suhu'             => 'nullable|numeric',
            'kelembapan'       => 'nullable|numeric',
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422); // Unprocessable Entity
        }

        // If validation passes, create the form
        $form = FormANeedleAssy::updateOrCreate($validator->validated());
        Log::create(
            [
                'action' => 'ADD FORM A NEDDLE ASSY',
                'created_date' => now(),
                'created_by' => $request->user()->nik,
                'created_at' => now(),
                'task_id' => $request->task_id,
            ]
        );

        // Return the created form as JSON
        return response()->json($form, 201);
    }

    // Show a specific record
    public function show($id): JsonResponse
    {
        try {
            $form = FormANeedleAssy::where('task_id', $id)->orderBy('id', 'desc')->first();

            if (!$form) {
                return response()->json(['message' => 'Form not found'], 404);
            }


            return response()->json(['data' => $form], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Update a specific record
    public function update(Request $request, $id): JsonResponse
    {
        // Find the form by ID
        $form = FormANeedleAssy::findOrFail($id);

        // Manually validate the incoming data
        $validator = Validator::make($request->all(), [
            'code_task'        => 'required|string',
            'tanggal'          => 'required|date',
            'sebelum_produk'   => 'required|string',
            'sebelum_bets'     => 'required|string',
            'sebelum_needle'   => 'required|string',
            'sebelum_cap'      => 'required|string',
            'bersih_palet'     => 'required|boolean',
            'bersih_lantai'    => 'required|boolean',
            'bersih_kolong'    => 'required|boolean',
            'bersih_mesin'     => 'required|boolean',
            'bersih_grill'     => 'required|boolean',
            'sisa_lantai'      => 'required|boolean',
            'sisa_hub'         => 'required|boolean',
            'sisa_cap'         => 'required|boolean',
            'sisa_canula'      => 'required|boolean',
            'sisa_box'         => 'nullable|boolean',
            'sisa_reject'      => 'nullable|boolean',
            'sisa_rework'      => 'nullable|boolean',
            'sebelum_dokumen'  => 'required|boolean',
            'material_sesuai'  => 'required|boolean',
            'saat_dokumen'     => 'required|boolean',
            'suhu'             => 'nullable|numeric',
            'kelembapan'       => 'nullable|numeric',
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

    // Delete a record
    public function destroy($id): JsonResponse
    {
        // Find the form by ID
        $form = FormANeedleAssy::findOrFail($id);

        // Delete the form
        $form->delete();

        // Return a success message
        return response()->json(['message' => 'Record deleted successfully']);
    }
}
