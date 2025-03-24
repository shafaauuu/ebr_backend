<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BoM;

class BoMSeeder extends Seeder
{
    public function run()
    {
        // Read the JSON file from storage/app/
        $json = file_get_contents(storage_path('app/cleaned_boms.json'));
        $data = json_decode($json, true);

        // Bulk insert directly (no loop needed)
        BoM::insert($data);
    }
}
