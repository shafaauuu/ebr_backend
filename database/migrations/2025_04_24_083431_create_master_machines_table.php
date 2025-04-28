<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('master_machines', function (Blueprint $table) {
            $table->id('id_machine');
            $table->string('machine_code', 10)->unique(); // e.g., mch001
            $table->string('machine_name', 100);          // e.g., HUALIAN 1
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_machines');
    }
};
