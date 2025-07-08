<?php

namespace App\Http\Controllers\FormD;

use App\Http\Controllers\Controller;
use App\Models\FormB\FormBAssySyringe;
use App\Models\FormB\FormBBlister;
use App\Models\FormB\FormBInjection;
use App\Models\FormB\FormBNeedleAssy;
use App\Models\FormD\DisplayMachineBlister;
use App\Models\FormD\DisplayMachineSgp;
use App\Models\FormD\FormD;
use App\Models\FormD\FormDDisplay;
use App\Models\FormD\DisplayMachineAssy;
use App\Models\FormD\DisplayMachineFcs;
use App\Models\FormD\DisplayMachineFcsShi;
use App\Models\FormD\DisplayMachineShi1;
use App\Models\FormD\DisplayMachineShi2;
use App\Models\FormD\MachineAssy;
use App\Models\FormD\MachineBlister;
use App\Models\FormD\MachineFcs;
use App\Models\FormD\MachineFcsShi;
use App\Models\FormD\MachineSgp;
use App\Models\FormD\MachineShi1;
use App\Models\FormD\MachineShi2;
use App\Models\Log;
use App\Models\MasterBrm;
use App\Models\MasterMachine;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FormDController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $forms = FormD::with(['machine', 'task'])->get();
        return response()->json($forms);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Log the incoming request for debugging
        \Log::info('Form D Request:', $request->all());

        $validator = Validator::make($request->all(), [
            'tanggal' => 'required|date',
            'line_clear' => 'required|boolean',
            'machine_id' => 'required|exists:master_machines,id_machine',
            'material_type' => 'required|string|max:100',
            'code_task' => 'required|string|max:50',
            'brm_no' => 'required|string|max:50',
            'shift' => 'required|string|max:2',
            'task_id' => 'required|exists:tasks,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();

        try {
            $formData = [
                'tanggal' => $request->tanggal,
                'line_clear' => $request->line_clear,
                'machine_id' => $request->machine_id,
                'material_type' => $request->material_type,
                'code_task' => $request->code_task,
                'brm_no' => $request->brm_no,
                'shift' => $request->shift,
                'task_id' => $request->task_id,
            ];

            $form = FormD::create($formData);

            // Log the submission
            Log::create([
                'action' => 'ADD FORM D',
                'created_date' => now(),
                'created_by' => $request->user() ? $request->user()->nik : 'system',
                'created_at' => now(),
                'task_id' => $request->task_id,
                'details' => json_encode([
                    'form_id' => $form->id_form_d,
                ]),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Form D created successfully',
                'data' => $form
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error submitting Form D: ' . $e->getMessage());
            \Log::error('Error trace: ' . $e->getTraceAsString());

            return response()->json([
                'message' => 'Failed to create Form D',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function task($taskId) {
        $table = [
            [FormBAssySyringe::class, "assysyringe"],
            [FormBBlister::class, "blister"],
            [FormBInjection::class, "injection"],
            [FormBNeedleAssy::class, "needleassy"]
        ];
        $form_b_data = null;
        $form_b_type = "";

        foreach ($table as $item) {
            $form_b_data = $item[0]::where('task_id', $taskId)->first();
            if ($form_b_data) {
                $form_b_type = $item[1];
                break;
            }
        }


        if($form_b_data == null) throw new \Exception('Form b must be filled');
        $machine = MasterMachine::where('machine_code', $form_b_data->machine_id)->first();
        if (!$machine) {
            return response()->json([
                'message' => 'Machine not found for the given machine code.',
            ], 404);
        }
        $displayTables = [
            [DisplayMachineAssy::class, "assy",MachineAssy::class],
            [DisplayMachineBlister::class,"blister", MachineBlister::class],
            [DisplayMachineFcs::class,"fcs", MachineFcs::class],
            [DisplayMachineFcsShi::class,"fcs_shi", MachineFcsShi::class],
            [DisplayMachineSgp::class,"sgp", MachineSgp::class],
            [DisplayMachineShi1::class,"shi_1", MachineShi1::class],
            [DisplayMachineShi2::class,"shi_2", MachineShi2::class],
        ];

        foreach ($displayTables as $model) {
            $display = $model[0]::where('machine_id', $machine->id_machine)
                ->with(['machine'])
                ->first();

            if ($display) {


                $display["form_type"] = $form_b_type;
                $display["machine_type"] = $model[1];
                $display["form_value"] = FormD::where('task_id', $taskId)
                    ->orderBy('id_form_d', 'desc')
                    ->first();
                if(isset($display['form_value']) &&  $display["form_value"]->id_form_d != null) {
                    $display["machine_value"] = $model[2]::where('form_d_id',$display["form_value"]->id_form_d )->first();
                }

                return response()->json($display);
            }
        }
    }
    /**
     * Display the specified resource.
     */
    public function show($id, Request $request)
    {
        try {
            $isTaskId = $request->query('is_task_id', true);

            if ($isTaskId) {
                // Get all records for the task
                $forms = FormD::where('task_id', $id)
                    ->with(['machine', 'task'])
                    ->orderBy('id_form_d', 'desc')
                    ->get();

                if ($forms->isEmpty()) {
                    return response()->json([
                        'message' => 'No Form D records found for this task',
                        'data' => null
                    ], 404);
                }

                return response()->json([
                    'message' => 'Form D records retrieved successfully',
                    'data' => $forms
                ]);
            } else {
                // Get a specific form by its ID
                $form = FormD::with(['machine', 'task'])->findOrFail($id);

                return response()->json([
                    'message' => 'Form D record retrieved successfully',
                    'data' => $form
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Error retrieving Form D: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to retrieve Form D',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Log the incoming request for debugging
        \Log::info('Form D Update Request:', $request->all());

        $form = FormD::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'tanggal' => 'nullable|date',
            'machine_id' => 'nullable|exists:master_machines,id_machine',
            'material_type' => 'nullable|string|max:10',
            'code_task' => 'nullable|string|max:50',
            'brm_no' => 'nullable|string|max:50',
            'shift' => 'nullable|string|max:2',
            'form_d_id' => 'nullable|exists:form_d_display,id_form_d_display',
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

            if ($request->has('tanggal')) {
                $updateData['tanggal'] = $request->tanggal;
            }

            if ($request->has('machine_id')) {
                $updateData['machine_id'] = $request->machine_id;
            }

            if ($request->has('material_type')) {
                $updateData['material_type'] = $request->material_type;
            }

            if ($request->has('code_task')) {
                $updateData['code_task'] = $request->code_task;
            }

            if ($request->has('brm_no')) {
                $updateData['brm_no'] = $request->brm_no;
            }

            if ($request->has('shift')) {
                $updateData['shift'] = $request->shift;
            }

            if ($request->has('form_d_id')) {
                $updateData['form_d_id'] = $request->form_d_id;
            }

            $form->update($updateData);

            // Log the update
            Log::create([
                'action' => 'UPDATE FORM D',
                'created_date' => now(),
                'created_by' => $request->user() ? $request->user()->nik : 'system',
                'created_at' => now(),
                'task_id' => $form->task_id,
                'details' => json_encode([
                    'form_id' => $form->id_form_d,
                ]),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Form D updated successfully',
                'data' => $form
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating Form D: ' . $e->getMessage());
            \Log::error('Error trace: ' . $e->getTraceAsString());

            return response()->json([
                'message' => 'Failed to update Form D',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id, Request $request)
    {
        DB::beginTransaction();

        try {
            $form = FormD::findOrFail($id);
            $form->delete();

            // Log the deletion
            Log::create([
                'action' => 'DELETE FORM D',
                'created_date' => now(),
                'created_by' => $request->user() ? $request->user()->nik : 'system',
                'created_at' => now(),
                'task_id' => $form->task_id,
                'details' => json_encode([
                    'form_id' => $id,
                ]),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Form D deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting Form D: ' . $e->getMessage());
            \Log::error('Error trace: ' . $e->getTraceAsString());

            return response()->json([
                'message' => 'Failed to delete Form D',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get form D data by machine ID
     */
    public function getByMachineId($machineId)
    {
        try {
            $forms = FormD::where('machine_id', $machineId)
                ->with(['machine', 'task'])
                ->orderBy('id_form_d', 'desc')
                ->get();

            if ($forms->isEmpty()) {
                return response()->json([
                    'message' => 'No Form D records found for this machine',
                    'data' => []
                ]);
            }

            return response()->json([
                'message' => 'Form D records retrieved successfully',
                'data' => $forms
            ]);
        } catch (\Exception $e) {
            \Log::error('Error retrieving Form D by machine ID: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to retrieve Form D records',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get form D data by form_d_id (display ID)
     */
    public function getByDisplayId($displayId)
    {
        try {
            $forms = FormD::where('form_d_id', $displayId)
                ->with(['machine', 'task'])
                ->orderBy('id_form_d', 'desc')
                ->get();

            if ($forms->isEmpty()) {
                return response()->json([
                    'message' => 'No Form D records found for this display',
                    'data' => []
                ]);
            }

            return response()->json([
                'message' => 'Form D records retrieved successfully',
                'data' => $forms
            ]);
        } catch (\Exception $e) {
            \Log::error('Error retrieving Form D by display ID: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to retrieve Form D records',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get form data with machine display values based on BRM and machine ID
     */
    public function getFormWithMachineDisplay(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'brm_no' => 'required|string|max:50',
                'machine_id' => 'required|exists:master_machines,id_machine',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $brmNo = $request->brm_no;
            $machineId = $request->machine_id;

            // Get machine info from master_machines
            $machine = DB::table('master_machines')
                ->where('id_machine', $machineId)
                ->first();

            if (!$machine) {
                return response()->json([
                    'message' => 'Machine not found',
                ], 404);
            }

            // Get form_d_display configuration for this machine
            $formDDisplay = FormDDisplay::where('machine_id', $machineId)->first();

            if (!$formDDisplay) {
                return response()->json([
                    'message' => 'Form D display configuration not found for this machine',
                ], 404);
            }

            // Determine machine type based on machine name or code
            $machineType = $this->determineMachineType($machine->machine_name, $machine->machine_code);

            // Debug information if machine type can't be determined
            if (!$machineType) {
                \Log::info('Unable to determine machine type', [
                    'machine_id' => $machineId,
                    'machine_name' => $machine->machine_name,
                    'machine_code' => $machine->machine_code
                ]);

                // Default to a machine type based on the machine ID for testing
                // You can remove this after debugging
                $machineTypes = ['assy', 'fcs', 'fcs_shi', 'shi_1', 'shi_2'];
                $machineType = $machineTypes[($machineId % 5)]; // Simple way to assign a type for testing

                return response()->json([
                    'message' => 'Unable to determine machine type',
                    'debug_info' => [
                        'machine_id' => $machineId,
                        'machine_name' => $machine->machine_name,
                        'machine_code' => $machine->machine_code
                    ],
                    'suggested_machine_type' => $machineType
                ], 400);
            }

            // Get machine-specific display data based on machine type and BRM
            $displayData = null;
            $defaultDisplayData = null;

            switch ($machineType) {
                case 'assy':
                    // Try to get BRM-specific data first with exact match
                    $displayData = DisplayMachineAssy::where('brm_no', $brmNo)
                        ->where('machine_id', $machineId)
                        ->first();

                    // If no exact match, try partial match (e.g., "BRM - 0187" would match "BRM - 0187 Rev.02")
                    if (!$displayData) {
                        // Extract the base BRM number without revision
                        $baseBrmNo = preg_replace('/\s+Rev\.\d+$/i', '', $brmNo);
                        \Log::info('Trying partial BRM match', [
                            'original_brm' => $brmNo,
                            'base_brm' => $baseBrmNo
                        ]);

                        $displayData = DisplayMachineAssy::where('brm_no', 'LIKE', $baseBrmNo . '%')
                            ->where('machine_id', $machineId)
                            ->first();
                    }

                    // If still no match, get the default data for this machine (any BRM)
                    if (!$displayData) {
                        $defaultDisplayData = DisplayMachineAssy::where('machine_id', $machineId)
                            ->first();
                    }
                    break;
                case 'fcs':
                    // Try exact match
                    $displayData = DisplayMachineFcs::where('brm_no', $brmNo)
                        ->where('machine_id', $machineId)
                        ->first();

                    // Try partial match
                    if (!$displayData) {
                        $baseBrmNo = preg_replace('/\s+Rev\.\d+$/i', '', $brmNo);
                        $displayData = DisplayMachineFcs::where('brm_no', 'LIKE', $baseBrmNo . '%')
                            ->where('machine_id', $machineId)
                            ->first();
                    }

                    if (!$displayData) {
                        $defaultDisplayData = DisplayMachineFcs::where('machine_id', $machineId)
                            ->first();
                    }
                    break;
                case 'fcs_shi':
                    // Try exact match
                    $displayData = DisplayMachineFcsShi::where('brm_no', $brmNo)
                        ->where('machine_id', $machineId)
                        ->first();

                    // Try partial match
                    if (!$displayData) {
                        $baseBrmNo = preg_replace('/\s+Rev\.\d+$/i', '', $brmNo);
                        $displayData = DisplayMachineFcsShi::where('brm_no', 'LIKE', $baseBrmNo . '%')
                            ->where('machine_id', $machineId)
                            ->first();
                    }

                    if (!$displayData) {
                        $defaultDisplayData = DisplayMachineFcsShi::where('machine_id', $machineId)
                            ->first();
                    }
                    break;
                case 'shi_1':
                    // Try exact match
                    $displayData = DisplayMachineShi1::where('brm_no', $brmNo)
                        ->where('machine_id', $machineId)
                        ->first();

                    // Try partial match
                    if (!$displayData) {
                        $baseBrmNo = preg_replace('/\s+Rev\.\d+$/i', '', $brmNo);
                        $displayData = DisplayMachineShi1::where('brm_no', 'LIKE', $baseBrmNo . '%')
                            ->where('machine_id', $machineId)
                            ->first();
                    }

                    if (!$displayData) {
                        $defaultDisplayData = DisplayMachineShi1::where('machine_id', $machineId)
                            ->first();
                    }
                    break;
                case 'shi_2':
                    // Try exact match
                    $displayData = DisplayMachineShi2::where('brm_no', $brmNo)
                        ->where('machine_id', $machineId)
                        ->first();

                    // Try partial match
                    if (!$displayData) {
                        $baseBrmNo = preg_replace('/\s+Rev\.\d+$/i', '', $brmNo);
                        \Log::info('Trying partial BRM match for SHI-2', [
                            'original_brm' => $brmNo,
                            'base_brm' => $baseBrmNo
                        ]);

                        $displayData = DisplayMachineShi2::where('brm_no', 'LIKE', $baseBrmNo . '%')
                            ->where('machine_id', $machineId)
                            ->first();
                    }

                    if (!$displayData) {
                        $defaultDisplayData = DisplayMachineShi2::where('machine_id', $machineId)
                            ->first();
                    }
                    break;
                default:
                    return response()->json([
                        'message' => 'Invalid machine type',
                    ], 400);
            }

            // If no BRM-specific display data found, use the default data
            if (!$displayData && $defaultDisplayData) {
                return response()->json([
                    'message' => 'No BRM-specific display data found, using default machine values',
                    'machine' => $machine,
                    'machine_type' => $machineType,
                    'form_d_display' => $formDDisplay,
                    'display_data' => $defaultDisplayData,
                    'is_default_data' => true
                ], 200);
            }

            // If no display data found at all
            if (!$displayData && !$defaultDisplayData) {
                // Create empty display data structure based on machine type
                $emptyDisplayData = $this->createEmptyDisplayData($machineType);

                return response()->json([
                    'message' => 'No display data found for this BRM and machine',
                    'machine' => $machine,
                    'machine_type' => $machineType,
                    'form_d_display' => $formDDisplay,
                    'display_data' => $emptyDisplayData,
                    'is_empty_data' => true
                ], 200);
            }

            return response()->json([
                'message' => 'Machine display data retrieved successfully',
                'machine' => $machine,
                'machine_type' => $machineType,
                'form_d_display' => $formDDisplay,
                'display_data' => $displayData,
                'is_default_data' => false
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Error getting machine display data: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to get machine display data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create empty display data structure based on machine type
     */
    private function createEmptyDisplayData($machineType)
    {
        $emptyData = [
            'machine_id' => null,
            'brm_no' => null,
            'created_at' => null,
            'updated_at' => null
        ];

        switch ($machineType) {
            case 'assy':
                return array_merge($emptyData, [
                    'print_match_speed' => '',
                    'assy_match_speed' => '',
                    'load_barrel' => false,
                    'load_plunger' => false,
                    'load_gasket' => false,
                    'actual_running' => '',
                    'run_awal' => '',
                    'defect' => '',
                    'goods_ok' => '',
                    'goods_reject' => ''
                ]);
            case 'fcs':
                return array_merge($emptyData, [
                    'open_position_slow' => '',
                    'open_position_fast' => '',
                    'open_position_mid' => '',
                    'open_position_dec' => '',
                    'open_speed_slow' => '',
                    'open_speed_fast' => '',
                    'open_speed_mid' => '',
                    'open_speed_dec' => '',
                    'sealing_temperature' => '',
                    'open_pressure_slow' => '',
                    'open_pressure_fast' => '',
                    'open_pressure_mid' => '',
                    'open_pressure_dec' => '',
                    // Add more FCS fields as needed
                ]);
            case 'fcs_shi':
                return array_merge($emptyData, [
                    'temp_nozzle_z1' => '',
                    'temp_nozzle_z2' => '',
                    'temp_nozzle_z3' => '',
                    'temp_nozzle_z4' => '',
                    'temp_nozzle_z5' => '',
                    'temp_mold' => '',
                    'inject_pressure' => '',
                    'inject_time' => '',
                    'holding_pressure' => '',
                    'holding_time' => '',
                    'eject_counter' => '',
                    'cycle_time' => '',
                    'material_type' => ''
                ]);
            case 'shi_1':
                return array_merge($emptyData, [
                    'open_position_openlimit' => '',
                    'open_position_second' => '',
                    'open_position_first' => '',
                    'open_velocity_openlimit' => '',
                    'open_velocity_second' => '',
                    'open_velocity_first' => '',
                    // Add more SHI-1 fields as needed
                ]);
            case 'shi_2':
                return array_merge($emptyData, [
                    'open_position_openlimit' => '',
                    'open_position_forth' => '',
                    'open_position_third' => '',
                    'open_position_second' => '',
                    'open_position_first' => '',
                    // Add more SHI-2 fields as needed
                ]);
            default:
                return $emptyData;
        }
    }

    /**
     * Store machine-specific data based on form submission
     */
    public function storeMachineData(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'form_d_id' => 'required|exists:form_d,id_form_d',
                'machine_type' => 'required|string|in:assy,fcs,fcs_shi,shi_1,shi_2',
                'task_id' => 'required|exists:tasks,id',
                'code_task' => 'required|string|max:50',
                'machine_id' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $formDId = $request->form_d_id;
            $machineType = $request->machine_type;
            $taskId = $request->task_id;
            $machineId = $request->machine_id;

            DB::beginTransaction();

            try {
                // Get the form_d record
                $formD = FormD::findOrFail($formDId);

                // Store data in the appropriate machine table based on machine type
                $result = null;
                switch ($machineType) {
                    case 'assy':
                        $result = $this->storeMachineAssyData($request, $formDId, $taskId, $machineId);
                        break;
                    case 'fcs':
                        $result = $this->storeMachineFcsData($request, $formDId, $taskId, $machineId);
                        break;
                    case 'fcs_shi':
                        $result = $this->storeMachineFcsShiData($request, $formDId, $taskId, $machineId);
                        break;
                    case 'shi_1':
                        $result = $this->storeMachineShi1Data($request, $formDId, $taskId, $machineId);
                        break;
                    case 'shi_2':
                        $result = $this->storeMachineShi2Data($request, $formDId, $taskId, $machineId);
                        break;
                    default:
                        return response()->json([
                            'message' => 'Invalid machine type',
                        ], 400);
                }

                // Log the submission
                Log::create([
                    'action' => 'ADD FORM D MACHINE DATA: ' . strtoupper($machineType),
                    'created_date' => now(),
                    'created_by' => $request->user() ? $request->user()->nik : 'system',
                    'created_at' => now(),
                    'task_id' => $taskId,
                    'details' => json_encode([
                        'form_d_id' => $formDId,
                        'machine_type' => $machineType
                    ]),
                ]);

                DB::commit();

                return response()->json([
                    'message' => 'Machine data stored successfully',
                    'form_d_id' => $formDId,
                    'machine_type' => $machineType,
                    'data' => $result
                ], 201);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            \Log::error('Error storing machine data: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to store machine data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store data for Machine Assy
     */
    private function storeMachineAssyData(Request $request, $formDId, $taskId, $machineId)
    {
        $data = $request->only([
            'code_task', 'print_match_speed', 'assy_match_speed', 'load_barrel',
            'load_plunger', 'load_gasket', 'actual_running', 'run_awal',
            'defect', 'goods_ok', 'goods_reject'
        ]);

        $data['form_d_id'] = $formDId;
        $data['task_id'] = $taskId;
        $data['machine_id'] = $machineId;

        // Handle file uploads if present
        if ($request->hasFile('approval')) {
            $data['approval'] = file_get_contents($request->file('approval')->path());
        }

        if ($request->hasFile('qc_barrel')) {
            $data['qc_barrel'] = file_get_contents($request->file('qc_barrel')->path());
        }

        if ($request->hasFile('qc_gasket')) {
            $data['qc_gasket'] = file_get_contents($request->file('qc_gasket')->path());
        }

        if ($request->hasFile('qc_plunger')) {
            $data['qc_plunger'] = file_get_contents($request->file('qc_plunger')->path());
        }

        if ($request->hasFile('machine_picture')) {
            $data['machine_picture'] = file_get_contents($request->file('machine_picture')->path());
        }

        $machineAssy = MachineAssy::create($data);
        return $machineAssy;
    }

    /**
     * Store data for Machine FCS
     */
    private function storeMachineFcsData(Request $request, $formDId, $taskId, $machineId)
    {
        // Extract all FCS-specific fields from request
        $data = $request->except(['form_d_id', 'task_id', 'machine_type', '_token']);

        $data['form_d_id'] = $formDId;
        $data['task_id'] = $taskId;
        $data['machine_id'] = $machineId;

        // Handle file uploads if present
        if ($request->hasFile('approval_hasil_printing')) {
            $data['approval_hasil_printing'] = file_get_contents($request->file('approval_hasil_printing')->path());
        }

        if ($request->hasFile('machine_picture')) {
            $data['machine_picture'] = file_get_contents($request->file('machine_picture')->path());
        }

        $machineFcs = MachineFcs::create($data);
        return $machineFcs;
    }

    /**
     * Store data for Machine FCS SHI
     */
    private function storeMachineFcsShiData(Request $request, $formDId, $taskId, $machineId)
    {
        $validator = Validator::make($request->all(), [
            'code_task' => 'required|string|max:50',
            'temp_nozzle_z1' => 'nullable|string|max:20',
            'temp_nozzle_z2' => 'nullable|string|max:20',
            'temp_nozzle_z3' => 'nullable|string|max:20',
            'temp_nozzle_z4' => 'nullable|string|max:20',
            'temp_nozzle_z5' => 'nullable|string|max:20',
            'temp_mold' => 'nullable|string|max:20',
            'inject_pressure' => 'nullable|string|max:30',
            'inject_time' => 'nullable|string|max:20',
            'holding_pressure' => 'nullable|string|max:30',
            'holding_time' => 'nullable|string|max:20',
            'eject_counter' => 'nullable|string|max:10',
            'cycle_time' => 'nullable|string|max:20',
            'material_type' => 'nullable|string|max:50',
            'approval' => 'nullable|file',
            'machine_picture' => 'nullable|file',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ];
        }

        // Handle file uploads
        $approvalFile = null;
        $machinePictureFile = null;

        if ($request->hasFile('approval')) {
            $approvalFile = file_get_contents($request->file('approval')->getPathname());
        }

        if ($request->hasFile('machine_picture')) {
            $machinePictureFile = file_get_contents($request->file('machine_picture')->getPathname());
        }

        // Create machine data record
        $machineData = MachineFcsShi::create([
            'form_d_id' => $formDId,
            'task_id' => $taskId,
            'machine_id' => $machineId,
            'code_task' => $request->code_task,
            'temp_nozzle_z1' => $request->temp_nozzle_z1,
            'temp_nozzle_z2' => $request->temp_nozzle_z2,
            'temp_nozzle_z3' => $request->temp_nozzle_z3,
            'temp_nozzle_z4' => $request->temp_nozzle_z4,
            'temp_nozzle_z5' => $request->temp_nozzle_z5,
            'temp_mold' => $request->temp_mold,
            'inject_pressure' => $request->inject_pressure,
            'inject_time' => $request->inject_time,
            'holding_pressure' => $request->holding_pressure,
            'holding_time' => $request->holding_time,
            'eject_counter' => $request->eject_counter,
            'cycle_time' => $request->cycle_time,
            'material_type' => $request->material_type,
            'approval' => $approvalFile,
            'machine_picture' => $machinePictureFile,
        ]);

        return [
            'success' => true,
            'message' => 'FCS-SHI machine data stored successfully',
            'data' => $machineData,
        ];
    }

    /**
     * Store data for Machine SHI-1
     */
    private function storeMachineShi1Data(Request $request, $formDId, $taskId, $machineId)
    {
        // Extract all SHI-1 specific fields from request
        $data = $request->except(['form_d_id', 'task_id', 'machine_type', '_token']);

        $data['form_d_id'] = $formDId;
        $data['task_id'] = $taskId;
        $data['machine_id'] = $machineId;

        if ($request->hasFile('machine_picture')) {
            $data['machine_picture'] = file_get_contents($request->file('machine_picture')->path());
        }

        $machineShi1 = MachineShi1::create($data);
        return $machineShi1;
    }

    /**
     * Store data for Machine SHI-2
     */
    private function storeMachineShi2Data(Request $request, $formDId, $taskId, $machineId)
    {
        // Extract all SHI-2 specific fields from request
        $data = $request->except(['form_d_id', 'task_id', 'machine_type', '_token']);

        $data['form_d_id'] = $formDId;
        $data['task_id'] = $taskId;
        $data['machine_id'] = $machineId;

        if ($request->hasFile('machine_picture')) {
            $data['machine_picture'] = file_get_contents($request->file('machine_picture')->path());
        }

        $machineShi2 = MachineShi2::create($data);
        return $machineShi2;
    }

    /**
     * Determine machine type based on machine name or code
     */
    private function determineMachineType($machineName, $machineCode)
    {
        // Handle null values
        $machineName = strtolower($machineName ?? '');
        $machineCode = strtolower($machineCode ?? '');

        // Log the input for debugging
        \Log::info('Determining machine type', [
            'machine_name' => $machineName,
            'machine_code' => $machineCode
        ]);

        // Special case for specific machine IDs
        if ($machineCode === 'mch029' || $machineName === 'fcs-hn-200') {
            return 'fcs_shi';
        }

        // Special case for machine ID 19 - should be SHI-1
        if ($machineCode === 'mch019') {
            return 'shi_1';
        }

        // Special case for machine ID 25 - should be SHI-2
        if ($machineCode === 'mch025' || $machineName === 'shi 14') {
            return 'shi_2';
        }

        // Check machine name first with more flexible matching
        if (strpos($machineName, 'assy') !== false || strpos($machineName, 'assembly') !== false) {
            return 'assy';
        } elseif (strpos($machineName, 'fcs shi') !== false || strpos($machineName, 'fcs-shi') !== false ||
                 strpos($machineName, 'fcs_shi') !== false || strpos($machineName, 'hn-200') !== false) {
            return 'fcs_shi';
        } elseif (strpos($machineName, 'fcs') !== false) {
            return 'fcs';
        } elseif (strpos($machineName, 'shi 1') !== false || strpos($machineName, 'shi-1') !== false ||
                 strpos($machineName, 'shi_1') !== false || strpos($machineName, 'shi1') !== false) {
            return 'shi_1';
        } elseif (strpos($machineName, 'shi 2') !== false || strpos($machineName, 'shi-2') !== false ||
                 strpos($machineName, 'shi_2') !== false || strpos($machineName, 'shi2') !== false) {
            return 'shi_2';
        }

        // If machine name doesn't contain type, check machine code with more flexible matching
        if (strpos($machineCode, 'assy') !== false || strpos($machineCode, 'assembly') !== false) {
            return 'assy';
        } elseif ((strpos($machineCode, 'fcs') !== false && strpos($machineCode, 'shi') !== false) ||
                 strpos($machineCode, 'fcs_shi') !== false || strpos($machineCode, 'fcs-shi') !== false) {
            return 'fcs_shi';
        } elseif (strpos($machineCode, 'fcs') !== false) {
            return 'fcs';
        } elseif (strpos($machineCode, 'shi1') !== false || strpos($machineCode, 'shi-1') !== false ||
                 strpos($machineCode, 'shi_1') !== false || strpos($machineCode, 'shi 1') !== false) {
            return 'shi_1';
        } elseif (strpos($machineCode, 'shi2') !== false || strpos($machineCode, 'shi-2') !== false ||
                 strpos($machineCode, 'shi_2') !== false || strpos($machineCode, 'shi 2') !== false) {
            return 'shi_2';
        }

        // If still can't determine, try to guess from the first characters of the code
        if (substr($machineCode, 0, 1) === 'a') {
            return 'assy';
        } elseif (substr($machineCode, 0, 1) === 'f') {
            return 'fcs';
        } elseif (substr($machineCode, 0, 1) === 's') {
            // For SHI machines, try to determine if it's SHI-1 or SHI-2
            $lastChar = substr($machineCode, -1);
            if ($lastChar === '1') {
                return 'shi_1';
            } elseif ($lastChar === '2') {
                return 'shi_2';
            }
            // Default to SHI-1 if can't determine
            return 'shi_1';
        }

        // If we get here, try to map based on common machine naming patterns
        // This is a fallback mechanism that might need to be adjusted based on your actual data
        if (preg_match('/\b(as|asy|asm|asb)\b/i', $machineName) || preg_match('/\b(as|asy|asm|asb)\b/i', $machineCode)) {
            return 'assy';
        } elseif (preg_match('/\b(fc|fs)\b/i', $machineName) || preg_match('/\b(fc|fs)\b/i', $machineCode)) {
            return 'fcs';
        } elseif (preg_match('/\b(sh|si|sh1|si1)\b/i', $machineName) || preg_match('/\b(sh|si|sh1|si1)\b/i', $machineCode)) {
            return 'shi_1';
        } elseif (preg_match('/\b(sh2|si2)\b/i', $machineName) || preg_match('/\b(sh2|si2)\b/i', $machineCode)) {
            return 'shi_2';
        }

        // Last resort: return a default type based on machine ID
        // This is just for testing and should be removed in production
        return 'assy';
    }

    /**
     * Get machine type for a specific machine ID
     * This is a helper endpoint for debugging
     */
    public function getMachineType(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'machine_id' => 'required|exists:master_machines,id_machine',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $machineId = $request->machine_id;
            $machine = MasterMachine::find($machineId);

            if (!$machine) {
                return response()->json([
                    'message' => 'Machine not found'
                ], 404);
            }

            $machineType = $this->determineMachineType($machine->machine_name, $machine->machine_code);

            return response()->json([
                'machine_id' => $machineId,
                'machine_name' => $machine->machine_name,
                'machine_code' => $machine->machine_code,
                'detected_machine_type' => $machineType
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Error getting machine type: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error getting machine type',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get form data with machine display values based on Task ID
     * This follows the flow: Task -> BRM -> Machine -> Display Data
     */
    public function getFormWithMachineDisplayByTask(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'task_id' => 'required|exists:tasks,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $taskId = $request->task_id;
            $task = \App\Models\Task::with('masterBrm')->find($taskId);

            if (!$task) {
                return response()->json([
                    'message' => 'Task not found'
                ], 404);
            }

            if (!$task->masterBrm) {
                return response()->json([
                    'message' => 'BRM not found for this task'
                ], 404);
            }

            $brmNo = $task->masterBrm->brm_no;
            $brmMachine = $task->masterBrm->brm_machine;

            // Find the machine from brm_machine field
            $machine = MasterMachine::where('machine_name', 'LIKE', '%' . $brmMachine . '%')
                ->orWhere('machine_code', 'LIKE', '%' . $brmMachine . '%')
                ->first();

            if (!$machine) {
                return response()->json([
                    'message' => 'Machine not found for this BRM',
                    'brm_machine' => $brmMachine
                ], 404);
            }

            $machineId = $machine->id_machine;

            // Now we have the BRM and machine ID, we can use the existing method
            // to fetch the display data
            $displayRequest = new Request([
                'brm_no' => $brmNo,
                'machine_id' => $machineId
            ]);

            return $this->getFormWithMachineDisplay($displayRequest);

        } catch (\Exception $e) {
            \Log::error('Error getting form with machine display by task: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error getting form with machine display by task',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
