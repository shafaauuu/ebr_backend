<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterMaterial;

class MasterMaterialSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['material_code' => '1500000003', 'material_desc' => 'ADS 0.3ml K1, 23G x 1"', 'material_type' => 'ZFGD', 'material_group' => 'FG0001', 'material_uom' => 'PC','created_at' => now()],
            ['material_code' => '1500000013', 'material_desc' => 'Safeject Hypodermic Needle 27G x 3/8" EO', 'material_type' => 'ZFGD', 'material_group' => 'FG0001', 'material_uom' => 'PC','created_at' => now()],
            ['material_code' => '1400000013', 'material_desc' => 'ADS 0.05ml OJI2, 27G x 3/8" - Unsterile', 'material_type' => 'ZSFG', 'material_group' => 'SFG001', 'material_uom' => 'PC','created_at' => now()],
            ['material_code' => '1400000013', 'material_desc' => 'Safeject Hypo Needle 27G x 3/8" EO - Unst', 'material_type' => 'ZSFG', 'material_group' => 'SFG001', 'material_uom' => 'PC', 'created_at' => now()],
            ['material_code' => '1300000002', 'material_desc' => 'Barrel ADS K1 0.3/0.5ml', 'material_type' => 'ZCOM', 'material_group' => 'IM0001', 'material_uom' => 'PC', 'created_at' => now()],
            ['material_code' => '1300000013', 'material_desc' => 'Hub 21', 'material_type' => 'ZCOM', 'material_group' => 'IM0003', 'material_uom' => 'PC', 'created_at' => now()],
        ];
        MasterMaterial::insert($data);
    }
}
