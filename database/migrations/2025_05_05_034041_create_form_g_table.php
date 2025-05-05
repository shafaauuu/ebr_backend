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
        Schema::create('form_g', function (Blueprint $table) {
            $table->id();
            $table->string('remarks', 255)->nullable();
            $table->binary('signed_1')->nullable();
            $table->string('inisial_1', 3)->nullable();
            $table->binary('signed_2')->nullable();
            $table->string('inisial_2', 3)->nullable();
            $table->binary('signed_3')->nullable();
            $table->string('inisial_3', 3)->nullable();
            $table->string('task_code', 255)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_g');
    }
};
