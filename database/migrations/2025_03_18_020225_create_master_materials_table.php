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
        Schema::create('master_materials', function (Blueprint $table) {
            $table->bigIncrements('id_mat');
            $table->string('material_code', 10);
            $table->string('material_desc', 100);
            $table->char('material_type', 4);
            $table->char('material_group', 10);
            $table->char('material_uom', 5);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_materials');
    }
};
