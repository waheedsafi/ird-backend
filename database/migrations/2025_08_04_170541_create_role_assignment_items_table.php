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
        Schema::create('role_assignment_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('role_assignment_id');
            $table->foreign('role_assignment_id')->references('id')->on('role_assignments')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->unsignedBigInteger('assignee_role_id'); // which roles they can assign
            $table->foreign('assignee_role_id')->references('id')->on('roles')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->index(["role_assignment_id", 'assignee_role_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_assignment_items');
    }
};
