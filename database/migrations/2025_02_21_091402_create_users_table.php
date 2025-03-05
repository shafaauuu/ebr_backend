<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->string('nik', 7)->primary();
            $table->string('first_name', 20);
            $table->string('last_name', 20);
            $table->string('email', 100)->unique();
            $table->string('password', 32); // MD5 is 32 characters
            $table->string('position', 50);
            $table->string('div', 50);
            $table->string('dept', 50);
            $table->timestamps();
        });

        // Add CHECK constraint for email domain
        DB::statement("ALTER TABLE users ADD CONSTRAINT email_domain_check CHECK (email ~* '^[a-zA-Z0-9._%+-]+@(oneject\\.com|oneject\\.co\\.id)$');");
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};
