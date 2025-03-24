<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Log;
use Carbon\Carbon;

class LogSeeder extends Seeder
{
    public function run(): void
    {
        // Sample Data to generate code_task
        $brm_no = 'BRM-0010';
        $batch_no = 'B001';
        $date = Carbon::createFromFormat('Y-m-d', '2025-01-24')->format('ymd'); // Fixed date to generate code

        $code_task = $brm_no . $date . $batch_no; // BRM-0010250124B001

        // Insert sample log entries
        Log::create([
            'code_task'    => $code_task,
            'action'       => 'ADD',
            'created_date' => '2025-01-24 08:30:10',
            'created_by'   => '240360',
            'created_at'   => now(),
        ]);

        Log::create([
            'code_task'    => $code_task,
            'action'       => 'COMPLETED',
            'created_date' => '2025-01-25 13:10:22',
            'created_by'   => '240210',
            'created_at'   => now(),
        ]);
    }
}
