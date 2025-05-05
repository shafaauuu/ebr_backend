<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterMaterial;
use App\Models\MasterBrm;
use Illuminate\Support\Facades\Log;

class MaterialController extends Controller
{
    public function search(Request $request)
    {
        try {
            $search = $request->input('search');
            $brm = $request->input('brm');

            $query = MasterBrm::query()
                ->join('master_materials', 'master_brms.material_code', '=', 'master_materials.material_code')
                ->select(
                    'master_materials.material_code',
                    'master_materials.material_desc',
                    'master_materials.material_group'
                )
                ->groupBy(
                    'master_materials.material_code',
                    'master_materials.material_desc',
                    'master_materials.material_group'
                );

            if ($brm) {
                $query->whereRaw("TRIM(master_brms.brm_no) = ?", [$brm]);
            }

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('master_materials.material_code', 'ILIKE', "%{$search}%")
                        ->orWhere('master_materials.material_desc', 'ILIKE', "%{$search}%")
                        ->orWhereRaw("TRIM(master_materials.material_group) ILIKE ?", ["%{$search}%"]);
                });
            }

            return response()->json($query->get());

        } catch (\Throwable $e) {
            Log::error('Material search error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
}
