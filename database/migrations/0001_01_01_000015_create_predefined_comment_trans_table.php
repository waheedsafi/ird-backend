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
        Schema::create('predefined_comment_trans', function (Blueprint $table) {
            $table->id();
            $table->text('value');
            $table->unsignedBigInteger('predefined_comment_id');
            $table->foreign('predefined_comment_id')->references('id')->on('predefined_comments')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->string('language_name');
            $table->foreign('language_name')->references('name')->on('languages')->onUpdate('cascade')
                ->onDelete('cascade');
            $table->index(["language_name", "predefined_comment_id"]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comment_trans');
    }
};
