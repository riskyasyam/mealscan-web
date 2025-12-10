<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('face_embeddings', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 50);
            $table->string('embedding_path', 255);
            $table->string('face_image_path', 255)->nullable();
            $table->float('confidence_score')->nullable();
            $table->json('bbox')->nullable();
            $table->timestamps();
            
            $table->foreign('nik')
                  ->references('nik')
                  ->on('employees')
                  ->onDelete('cascade');
            
            $table->index('nik');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('face_embeddings');
    }
};
