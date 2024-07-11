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
        Schema::create('user_value', function (Blueprint $table) {
            $table->bigInteger('user_id');
            $table->string('value_name');
                    
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('value_name')->references('name')->on('values')->cascadeOnUpdate()->restrictOnDelete();
            $table->primary(['user_id', 'value_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_value');
    }
};
