<?php

namespace App\Http\Controllers\FormA;

use App\Http\Controllers\Controller;
use App\Models\FormA\FormAAssySyringe;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FormAAssySyringeController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        try {
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

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $form = FormAAssySyringe::create($validator->validated());

            return response()->json([
                'message' => 'Form submitted successfully',
                'data' => $form
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
