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
        Schema::create('about_staff', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('about_staff_type_id');
            $table->foreign('about_staff_type_id')->references('id')->on('about_staff_types')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->string('contact', 32)->unique();
            $table->string('email', 64)->unique();
            $table->string('profile');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('about_staff');
    }
};
