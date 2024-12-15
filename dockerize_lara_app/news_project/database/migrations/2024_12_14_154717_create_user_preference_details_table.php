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
        Schema::create('user_preference_details', function (Blueprint $table) {
            $table->id();
            $table->index('user_preference_id');
            $table->unsignedBigInteger('user_preference_id');
            $table->index('category_id');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('author'); 
            $table->string('source'); 
            $table->timestamps();
            $table->foreign('user_preference_id')->references('id')->on('user_preferences')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
      
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_preference_details');
    }
};
