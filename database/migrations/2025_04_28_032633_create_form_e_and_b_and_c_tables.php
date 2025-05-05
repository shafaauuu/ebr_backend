<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Form E Injection
        Schema::create('form_e_injection', function (Blueprint $table) {
            $table->id();
            $table->string('code_task')->nullable();
            $table->integer('jml_teoritis')->nullable();
            $table->integer('jml_release')->nullable();
            $table->integer('jml_karantina')->nullable();
            $table->integer('jml_reject')->nullable();
            $table->integer('sample_ipc')->nullable();
            $table->integer('sample_qc')->nullable();
            $table->integer('sample_release')->nullable();
            $table->decimal('yield', 8, 2)->nullable();
            $table->integer('total_hasil')->nullable();
            $table->timestamps();
        });

        // Form E Blister
        Schema::create('form_e_blister', function (Blueprint $table) {
            $table->id();
            $table->string('code_task')->nullable();
            $table->integer('jml_awal_assy')->nullable();
            $table->integer('jml_karantina_assy')->nullable();
            $table->integer('total_syringe')->nullable();
            $table->integer('jml_fg')->nullable();
            $table->integer('jml_karantina')->nullable();
            $table->integer('jml_reject')->nullable();
            $table->integer('jml_sisa')->nullable();
            $table->integer('sample_ipc')->nullable();
            $table->integer('sample_qc')->nullable();
            $table->timestamps();
        });

        // Form E Needle Assy
        Schema::create('form_e_needle_assy', function (Blueprint $table) {
            $table->id();
            $table->string('code_task')->nullable();
            $table->integer('jml_teoritis')->nullable();
            $table->integer('jml_release')->nullable();
            $table->integer('jml_karantina')->nullable();
            $table->integer('jml_reject')->nullable();
            $table->integer('sample_ipc')->nullable();
            $table->integer('sample_qc')->nullable();
            $table->integer('sample_release')->nullable();
            $table->decimal('yield', 8, 2)->nullable();
            $table->integer('total_hasil')->nullable();
            $table->timestamps();
        });

        // Form E Assy Syringe
        Schema::create('form_e_assy_syringe', function (Blueprint $table) {
            $table->id();
            $table->string('code_task')->nullable();
            $table->integer('jml_teoritis')->nullable();
            $table->integer('jml_release')->nullable();
            $table->integer('jml_karantina')->nullable();
            $table->integer('jml_reject')->nullable();
            $table->integer('sample_ipc')->nullable();
            $table->integer('sample_qc')->nullable();
            $table->integer('sample_release')->nullable();
            $table->decimal('yield', 8, 2)->nullable();
            $table->integer('total_hasil')->nullable();
            $table->timestamps();
        });

        // Form B Machine Qualification
        Schema::create('form_b_assy_syringe', function (Blueprint $table) {
            $table->id();
            $table->string('code_task')->nullable();
            $table->string('machine_id')->nullable();
            $table->boolean('terkualifikasi')->nullable();
            $table->timestamps();
        });

        Schema::create('form_b_injection', function (Blueprint $table) {
            $table->id();
            $table->string('code_task')->nullable();
            $table->string('machine_id')->nullable();
            $table->boolean('terkualifikasi')->nullable();
            $table->timestamps();
        });

        Schema::create('form_b_blister', function (Blueprint $table) {
            $table->id();
            $table->string('code_task')->nullable();
            $table->string('machine_id')->nullable();
            $table->boolean('terkualifikasi')->nullable();
            $table->timestamps();
        });

        Schema::create('form_b_needle_assy', function (Blueprint $table) {
            $table->id();
            $table->string('code_task')->nullable();
            $table->string('machine_id')->nullable();
            $table->boolean('terkualifikasi')->nullable();
            $table->timestamps();
        });

        // Form C Materials
        Schema::create('form_c_assy_syringe', function (Blueprint $table) {
            $table->id();
            $table->string('code_task')->nullable();
            $table->string('id_brm')->nullable();
            $table->string('material_code')->nullable();
            $table->string('batch_no')->nullable();
            $table->integer('actual_qty')->nullable();
            $table->boolean('sesuai_picklist')->nullable();
            $table->string('remarks_picklist', 255)->nullable();
            $table->boolean('sesuai_bets')->nullable();
            $table->string('remarks_bets', 255)->nullable();
            $table->boolean('mat_lengkap')->nullable();
            $table->string('remarks_mat', 255)->nullable();
            $table->timestamps();
        });

        Schema::create('form_c_blister', function (Blueprint $table) {
            $table->id();
            $table->string('code_task')->nullable();
            $table->string('id_brm')->nullable();
            $table->string('material_code')->nullable();
            $table->string('batch_no')->nullable();
            $table->integer('actual_qty')->nullable();
            $table->boolean('sesuai_picklist')->nullable();
            $table->string('remarks_picklist', 255)->nullable();
            $table->boolean('sesuai_bets')->nullable();
            $table->string('remarks_bets', 255)->nullable();
            $table->boolean('mat_lengkap')->nullable();
            $table->string('remarks_mat', 255)->nullable();
            $table->timestamps();
        });

        Schema::create('form_c_injection', function (Blueprint $table) {
            $table->id();
            $table->string('code_task')->nullable();
            $table->string('id_brm')->nullable();
            $table->string('material_code')->nullable();
            $table->string('batch_no')->nullable();
            $table->integer('actual_qty')->nullable();
            $table->boolean('sesuai_picklist')->nullable();
            $table->string('remarks_picklist', 255)->nullable();
            $table->boolean('sesuai_bets')->nullable();
            $table->string('remarks_bets', 255)->nullable();
            $table->boolean('mat_lengkap')->nullable();
            $table->string('remarks_mat', 255)->nullable();
            $table->timestamps();
        });

        Schema::create('form_c_needle_assy', function (Blueprint $table) {
            $table->id();
            $table->string('code_task')->nullable();
            $table->string('id_brm')->nullable();
            $table->string('material_code')->nullable();
            $table->string('batch_no')->nullable();
            $table->integer('actual_qty')->nullable();
            $table->boolean('sesuai_picklist')->nullable();
            $table->string('remarks_picklist', 255)->nullable();
            $table->boolean('sesuai_bets')->nullable();
            $table->string('remarks_bets', 255)->nullable();
            $table->boolean('mat_lengkap')->nullable();
            $table->string('remarks_mat', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_e_injection');
        Schema::dropIfExists('form_e_blister');
        Schema::dropIfExists('form_e_needle_assy');
        Schema::dropIfExists('form_e_assy_syringe');
        Schema::dropIfExists('form_b_assy_syringe');
        Schema::dropIfExists('form_b_injection');
        Schema::dropIfExists('form_b_blister');
        Schema::dropIfExists('form_b_needle_assy');
        Schema::dropIfExists('form_c_assy_syringe');
        Schema::dropIfExists('form_c_blister');
        Schema::dropIfExists('form_c_injection');
        Schema::dropIfExists('form_c_needle_assy');
    }
};
