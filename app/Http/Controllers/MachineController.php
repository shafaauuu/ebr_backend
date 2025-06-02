<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterBrm;
use App\Models\MasterMachine;

class MachineController extends Controller
{
    public function getMachinesByBrm($brm_no): \Illuminate\Http\JsonResponse
    {
        // Fetch the BRM entry
        $brm = MasterBrm::where('brm_no', $brm_no)->first();

        if (!$brm) {
            return response()->json(['machines' => []]);
        }

        // Split machine codes by comma if needed
        $machineCodes = explode(',', $brm->brm_machine_id);

        // Fetch machines associated with the machine codes
        $machines = MasterMachine::whereIn('machine_code', $machineCodes)->get();

        return response()->json(['machines' => $machines]);
    }
}
