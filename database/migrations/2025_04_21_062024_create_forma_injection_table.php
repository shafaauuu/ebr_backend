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
        Schema::create('forma_injection', function (Blueprint $table) {
            $table->id();
            $table->string('code_task', 20);
            $table->date('tanggal');
            $table->char('sebelum_produk');
            $table->char('sebelum_bets');
            $table->boolean('bersih_lantai');
            $table->boolean('bersih_dinding');
            $table->boolean('bersih_grill');
            $table->boolean('bersih_alat');
            $table->boolean('sisa_produk');
            $table->boolean('sebelum_dokumen');
            $table->boolean('material_sesuai');
            $table->boolean('saat_dokumen');
            $table->decimal('suhu', 5, 2)->nullable();
            $table->decimal('kelembapan', 5, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forma_injection');
    }
};
