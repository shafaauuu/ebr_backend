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
        Schema::create('mapping_setting_mesin', function (Blueprint $table) {
            $table->id();
            $table->string('setting_mach')->nullable();
            $table->string('brm_no')->nullable();
            $table->string('machine_id')->nullable();
            $table->string('detail_material')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mapping_setting_mesin');
    }
};
