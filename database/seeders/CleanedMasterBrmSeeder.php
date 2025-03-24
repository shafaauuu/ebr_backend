<?php

namespace Database\Seeders;

use App\Models\MasterBrm;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CleanedMasterBrmSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['material_code' => '1400000001', 'brm_no' => 'BRM-0010', 'brm_machine' => 'KMD','created_at' => now()],
            ['material_code' => '1400000001', 'brm_no' => 'BRM-0010', 'brm_machine' => 'HUALIAN', 'created_at' => now()],
        ];
        MasterBrm::insert($data);
    }
}
