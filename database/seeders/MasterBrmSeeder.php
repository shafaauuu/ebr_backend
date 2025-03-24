<?php

namespace Database\Seeders;

use App\Models\MasterBrm;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class MasterBrmSeeder extends Seeder
{
    public function run()
    {
        // Read the JSON file from storage/app/
        $json = file_get_contents(storage_path('app/cleaned_master_brm.json'));
        $data = json_decode($json, true);

        // Bulk insert directly (no loop needed)
        MasterBrm::insert($data);
    }
}
