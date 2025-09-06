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
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->boolean('edit');
            $table->boolean('delete');
            $table->boolean('add');
            $table->boolean('view');
            $table->boolean("visible")->default(true);
            $table->unsignedBigInteger('role');
            $table->foreign('role')
                ->references('id')->on('roles')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->unsignedBigInteger('permission');
            $table->foreign('permission')
                ->references('id')->on('permissions')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->index(["permission", "role"]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
    }
};
