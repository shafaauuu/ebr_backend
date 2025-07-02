<?php

namespace App\Http\Controllers\FormD;

use App\Models\FormB\FormBInjection;
use App\Models\FormD\DisplayMachineAssy;
use App\Models\FormD\DisplayMachineBlister;
use App\Models\FormD\DisplayMachineFcs;
use App\Models\FormD\DisplayMachineFcsShi;
use App\Models\FormD\DisplayMachineSgp;
use App\Models\FormD\DisplayMachineShi1;
use App\Models\FormD\DisplayMachineShi2;
use App\Models\MasterMachine;

class FormDInjectionController
{
    public function getform($taskId)
    {
        try {
            $form_b_data = FormBInjection::where('task_id', $taskId)->first();

            if($form_b_data == null) throw new \Exception('Form b must be filled');
            $machine = MasterMachine::where('machine_code', $form_b_data->machine_id)->first();
            if (!$machine) {
                return response()->json([
                    'message' => 'Machine not found for the given machine code.',
                ], 404);
            }
            $displayTables = [
                DisplayMachineAssy::class,
                DisplayMachineBlister::class,
                DisplayMachineFcs::class,
                DisplayMachineFcsShi::class,
                DisplayMachineSgp::class,
                DisplayMachineShi1::class,
                DisplayMachineShi2::class,
            ];

            foreach ($displayTables as $model) {
                $display = $model::where('machine_id', $machine->id_machine)
                    ->with(['machine'])
                    ->first();

                if ($display) {
                    return response()->json($display);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error retrieving Display Machine Injection: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to retrieve Display Machine Injection',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
