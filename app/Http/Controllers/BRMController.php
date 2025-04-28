<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterBrm;

class BRMController extends Controller
{
    public function index()
    {
        return MasterBrm::all(); // to check if any issue is with the select

    }

    public function show($brmNo)
    {
        // Decode URL-encoded string
        $decodedBrmNo = urldecode($brmNo);

        // Log it (optional)
        \Log::info("Looking for BRM: " . $decodedBrmNo);

        // Find matching BRM
        $brm = MasterBrm::whereRaw('TRIM(brm_no) = ?', [$decodedBrmNo])->first();

        if ($brm) {
            return response()->json($brm);
        } else {
            return response()->json(['message' => 'BRM not found'], 404);
        }
    }

    public function getMaterials($brmNo)
    {
        $decodedBrmNo = urldecode($brmNo);

        // Get all rows with that BRM number
        $materials = MasterBrm::whereRaw('TRIM(brm_no) = ?', [$decodedBrmNo])
            ->pluck('material_code'); // only select material_code column

        return response()->json($materials);
    }

    public function getCategoryByBRM($brmNo)
    {
        $decodedBrmNo = urldecode($brmNo);

        \Log::info("Decoded BRM No: '$decodedBrmNo'");

        $brm = \DB::table('master_brms')
            ->join('brm_categories', 'master_brms.id_category', '=', 'brm_categories.id_category')
            ->whereRaw('TRIM(LOWER(master_brms.brm_no)) = ?', [strtolower(trim($decodedBrmNo))])
            ->select('brm_categories.category_name')
            ->first();

        if (!$brm) {
            \Log::warning("BRM NOT FOUND for: '$decodedBrmNo'");
            return response()->json(['error' => 'BRM not found'], 404);
        }

        return response()->json(['category' => $brm->category_name]);
    }

}
