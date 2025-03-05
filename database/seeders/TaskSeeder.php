<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskSeeder extends Seeder {
    public function run() {
        DB::table('tasks')->insert([
            [
                'code' => 'MLD-BRL-DS 5mL-LL-HRT',
                'task_name' => 'Packaging DS 5mL Safeject',
                'status' => 'ongoing',
                'assigned_by' => '2345678',
                'assigned_to' => '1234567',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'MLD-BRL-DS 6mL-LL-HRT',
                'task_name' => 'XXXXX-XX-XXX-Safeject',
                'status' => 'pending',
                'assigned_by' => '2345678',
                'assigned_to' => '1234567',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'MLD-BRL-DS 7mL-LL-HRT',
                'task_name' => 'XXXXX-XX-XXX-Safeject',
                'status' => 'completed',
                'assigned_by' => '2345678',
                'assigned_to' => '1234567',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'MLD-BRL-DS 8mL-LL-HRT',
                'task_name' => 'XXXXX-XX-XXX-Safeject',
                'status' => 'completed',
                'assigned_by' => '2345678',
                'assigned_to' => '1234567',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
