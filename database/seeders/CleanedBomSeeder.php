<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CleanedBomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['id_mat' => 1, 'material_code' => '1500000003', 'bom_level' => '1.1', 'child_mat' => '1400000003', 'created_at' => now()],
            ['id_mat' => 1, 'material_code' => '1500000003', 'bom_level' => '1.1.1', 'child_mat' => '1100000032', 'created_at' => now()],
            ['id_mat' => 1, 'material_code' => '1500000003', 'bom_level' => '1.1.2', 'child_mat' => '1100000276', 'created_at' => now()],
            ['id_mat' => 2, 'material_code' => '1500000013', 'bom_level' => '2.1', 'child_mat' => '1400000013', 'created_at' => now()],
            ['id_mat' => 2, 'material_code' => '1500000013', 'bom_level' => '2.1.1', 'child_mat' => '1100000032', 'created_at' => now()],
            ['id_mat' => 2, 'material_code' => '1500000013', 'bom_level' => '2.1.2', 'child_mat' => '1100000276', 'created_at' => now()],
        ];
        BoM::insert($data);
    }
}
