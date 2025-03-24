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
        Schema::create('boms', function (Blueprint $table) {
            $table->bigIncrements('id_bom');
            $table->unsignedBigInteger('id_mat');
            $table->string('material_code', 10);
            $table->string('bom_level', 10);
            $table->string('child_mat', 10);
            $table->timestamps();

            $table->foreign('id_mat')->references('id_mat')->on('master_materials')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boms');
    }
};
