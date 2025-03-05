<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('task_name');
            $table->enum('status', ['ongoing', 'pending', 'completed'])->default('ongoing');
            $table->string('assigned_by', 7); // Matches users.nik (7 characters)
            $table->string('assigned_to', 7); // Matches users.nik (7 characters)

            // Foreign keys referencing users.nik instead of users.id
            $table->foreign('assigned_by')->references('nik')->on('users')->onDelete('cascade');
            $table->foreign('assigned_to')->references('nik')->on('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('tasks');
    }
};
