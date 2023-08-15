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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->string('firstname', 64);
            $table->string('middlename', 64)->nullable();
            $table->string('lastname', 64);
            $table->tinyInteger('gender'); // 0 = FEMALE | 1 = MALE | 2 = NON-BINARY | 3 = HELICOPTER
            $table->date("birthdate")->nullable();
            $table->unsignedBigInteger('user_id')->unique(); // foreign key from users table column id
            $table->timestamps();

            $table->foreign("user_id")->references("id")->on("users")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
