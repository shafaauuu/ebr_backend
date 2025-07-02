<?php

namespace App\Http\Controllers\FormD;

use App\Models\FormB\FormBAssySyringe;
use App\Models\FormD\DisplayMachineAssy;
use App\Models\FormD\DisplayMachineBlister;
use App\Models\FormD\DisplayMachineFcs;
use App\Models\FormD\DisplayMachineFcsShi;
use App\Models\FormD\DisplayMachineSgp;
use App\Models\FormD\DisplayMachineShi1;
use App\Models\FormD\DisplayMachineShi2;
use App\Models\Log;
use App\Models\MasterMachine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FormDAssySyringeController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $displays = DisplayMachineAssy::with(['formDDisplay', 'machine'])->get();
        return response()->json($displays);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Log the incoming request for debugging
        \Log::info('Display Machine Assy Request:', $request->all());

        $validator = Validator::make($request->all(), [
            'brm_no' => 'required|string|max:50',
            'machine_id' => 'required|exists:form_d_display,machine_id',
            'material' => 'required|string|max:100',
            'print_mach_speed' => 'required|string|max:20',
            'assy_mach_speed' => 'required|string|max:40',
            'silicon_spray' => 'required|string|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Check if entry already exists for this machine_id and brm_no
            $existingEntry = DisplayMachineAssy::where('machine_id', $request->machine_id)
                ->where('brm_no', $request->brm_no)
                ->first();

            if ($existingEntry) {
                // Update existing entry
                $existingEntry->update($request->all());
                $display = $existingEntry;
            } else {
                // Create new entry
                $display = DisplayMachineAssy::create($request->all());
            }

            // Log the submission
            Log::create([
                'action' => 'ADD/UPDATE DISPLAY MACHINE ASSY',
                'created_date' => now(),
                'created_by' => $request->user() ? $request->user()->nik : 'system',
                'created_at' => now(),
                'details' => json_encode([
                    'brm_no' => $request->brm_no,
                    'machine_id' => $request->machine_id,
                ]),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Display Machine Assy data saved successfully',
                'data' => $display
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error saving Display Machine Assy: ' . $e->getMessage());
            \Log::error('Error trace: ' . $e->getTraceAsString());

            return response()->json([
                'message' => 'Failed to save Display Machine Assy data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            // Check if ID is a BRM number
            if (is_string($id) && !is_numeric($id)) {
                $display = DisplayMachineAssy::where('brm_no', $id)
                    ->with(['formDDisplay', 'machine'])
                    ->first();

                if (!$display) {
                    return response()->json([
                        'message' => 'No Display Machine Assy found for this BRM',
                        'data' => null
                    ], 404);
                }
            } else {
                // Assume it's a machine_id
                $display = DisplayMachineAssy::where('machine_id', $id)
                    ->with(['formDDisplay', 'machine'])
                    ->first();

                if (!$display) {
                    return response()->json([
                        'message' => 'No Display Machine Assy found for this machine',
                        'data' => null
                    ], 404);
                }
            }

            return response()->json([
                'message' => 'Display Machine Assy retrieved successfully',
                'data' => $display
            ]);
        } catch (\Exception $e) {
            \Log::error('Error retrieving Display Machine Assy: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to retrieve Display Machine Assy',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $machineId, $brmNo)
    {
        // Log the incoming request for debugging
        \Log::info('Display Machine Assy Update Request:', $request->all());

        $display = DisplayMachineAssy::where('machine_id', $machineId)
            ->where('brm_no', $brmNo)
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'material' => 'nullable|string|max:100',
            'print_mach_speed' => 'nullable|string|max:20',
            'assy_mach_speed' => 'nullable|string|max:40',
            'silicon_spray' => 'nullable|string|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();

        try {
            $updateData = [];

            if ($request->has('material')) {
                $updateData['material'] = $request->material;
            }

            if ($request->has('print_mach_speed')) {
                $updateData['print_mach_speed'] = $request->print_mach_speed;
            }

            if ($request->has('assy_mach_speed')) {
                $updateData['assy_mach_speed'] = $request->assy_mach_speed;
            }

            if ($request->has('silicon_spray')) {
                $updateData['silicon_spray'] = $request->silicon_spray;
            }

            $display->update($updateData);

            // Log the update
            Log::create([
                'action' => 'UPDATE DISPLAY MACHINE ASSY',
                'created_date' => now(),
                'created_by' => $request->user() ? $request->user()->nik : 'system',
                'created_at' => now(),
                'details' => json_encode([
                    'machine_id' => $machineId,
                    'brm_no' => $brmNo,
                ]),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Display Machine Assy updated successfully',
                'data' => $display
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating Display Machine Assy: ' . $e->getMessage());
            \Log::error('Error trace: ' . $e->getTraceAsString());

            return response()->json([
                'message' => 'Failed to update Display Machine Assy',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($machineId, $brmNo, Request $request)
    {
        DB::beginTransaction();

        try {
            $display = DisplayMachineAssy::where('machine_id', $machineId)
                ->where('brm_no', $brmNo)
                ->firstOrFail();

            $display->delete();

            // Log the deletion
            Log::create([
                'action' => 'DELETE DISPLAY MACHINE ASSY',
                'created_date' => now(),
                'created_by' => $request->user() ? $request->user()->nik : 'system',
                'created_at' => now(),
                'details' => json_encode([
                    'machine_id' => $machineId,
                    'brm_no' => $brmNo,
                ]),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Display Machine Assy deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting Display Machine Assy: ' . $e->getMessage());
            \Log::error('Error trace: ' . $e->getTraceAsString());

            return response()->json([
                'message' => 'Failed to delete Display Machine Assy',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getform($taskId)
    {
        try {
            $form_b_data = FormBAssySyringe::where('task_id', $taskId)->first();

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
            \Log::error('Error retrieving Display Machine Assy: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to retrieve Display Machine Assy',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get display data by BRM number
     */
    public function getByBrm($brmNo)
    {
        try {
            $display = DisplayMachineAssy::where('brm_no', $brmNo)
                ->with(['formDDisplay', 'machine'])
                ->first();

            if (!$display) {
                return response()->json([
                    'message' => 'No Display Machine Assy found for this BRM',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'message' => 'Display Machine Assy retrieved successfully',
                'data' => $display
            ]);
        } catch (\Exception $e) {
            \Log::error('Error retrieving Display Machine Assy by BRM: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to retrieve Display Machine Assy',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
