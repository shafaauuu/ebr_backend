<?php

namespace Database\Seeders;

use App\Models\BoM;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Seeder;

class KaryawanSeeder extends Seeder
{
    public function run()
    {
        // Read the JSON file from storage/app/
        $json = file_get_contents(storage_path('app/cleaned_karyawan.json'));
        $data = json_decode($json, true);

        // Bulk insert directly (no loop needed)
        User::insert($data);
    }
}
