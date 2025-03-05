<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('role_auth', function (Blueprint $table) {
            $table->id();
            $table->string('role', 50);
            $table->string('div', 50);
            $table->string('dept', 50);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('role_auth');
    }
};
