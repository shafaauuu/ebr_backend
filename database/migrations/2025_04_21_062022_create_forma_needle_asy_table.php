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
        Schema::create('form_a_needle_asy', function (Blueprint $table) {
            $table->id();
            $table->string('code_task', 20);
            $table->date('tanggal');
            $table->char('sebelum_produk');
            $table->char('sebelum_bets');
            $table->char('sebelum_needle');
            $table->char('sebelum_cap');
            $table->boolean('bersih_palet');
            $table->boolean('bersih_lantai');
            $table->boolean('bersih_kolong');
            $table->boolean('bersih_mesin');
            $table->boolean('bersih_grill');
            $table->boolean('sisa_lantai');
            $table->boolean('sisa_hub');
            $table->boolean('sisa_cap');
            $table->boolean('sisa_cannula');
            $table->boolean('sisa_box');
            $table->boolean('sisa_reject');
            $table->boolean('sisa_rework');
            $table->boolean('sebelum_dokumen');
            $table->boolean('material_sesuai');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forma_needle_asy');
    }
};
