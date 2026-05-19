<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_edits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('action'); // insert, delete, format, sync
            $table->unsignedInteger('document_version')->nullable();
            $table->unsignedInteger('caret_position')->nullable();
            $table->text('snippet')->nullable();
            $table->boolean('had_conflict')->default(false);
            $table->json('conflict_with')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_edits');
    }
};
