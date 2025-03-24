<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterMaterial;

class MasterMaterialSeeder extends Seeder
{
    public function run()
    {
        // Read the JSON file from storage/app/
        $json = file_get_contents(storage_path('app/cleaned_master_material.json'));
        $data = json_decode($json, true);

        foreach ($data as $item) {
            MasterMaterial::create($item);
        }
    }
}

