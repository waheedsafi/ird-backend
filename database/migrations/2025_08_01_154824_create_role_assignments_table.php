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
        Schema::create('role_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assigned_by');
            $table->foreign('assigned_by')->references('id')->on('users')->onDelete('no action');
            $table->unsignedBigInteger('assigner_role_id'); // who can assign
            $table->foreign('assigner_role_id')->references('id')->on('roles')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->unsignedBigInteger('permission');
            $table->foreign('permission')->references('id')->on('permissions')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->index(["assigner_role_id", "assigned_by", 'permission']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_assignments');
    }
};
