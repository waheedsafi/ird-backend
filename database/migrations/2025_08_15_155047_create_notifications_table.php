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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->unsignedBigInteger('sender_id');
            $table->foreign('sender_id')->references('id')->on('users')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->unsignedBigInteger('notifier_type_id');
            $table->foreign('notifier_type_id')->references('id')->on('notifier_types')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->boolean('is_read')->default(false);
            $table->string('action_url')->nullable();
            $table->json('context')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
