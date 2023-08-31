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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string("table_name")->nullable();
            $table->unsignedBigInteger("model_id")->nullable();
            $table->string("description");
            $table->unsignedBigInteger("user_id")->nullable();
            $table->longText("properties");
            $table->string("ip");
            $table->string("user_agent");

            $table->foreign("user_id")->references("id")->on("users");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
