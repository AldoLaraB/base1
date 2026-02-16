<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            
            // CHI possiede questo media (User, e in futuro Product, Post)
            $table->morphs('model');  // model_id e model_type
            // Ora: model_type = 'App\Models\User'
            // Futuro: model_type = 'App\Models\Product' o 'App\Models\Post'
            
            // TIPO di media (avatar, gallery, ecc.)
            $table->string('collection')->default('default')->index();
            
            // DOVE si trova il file
            $table->string('disk')->default('public');
            $table->string('path');
            $table->string('filename');
            
            // METADATI (utili sempre)
            $table->string('mime_type')->nullable();
            $table->unsignedInteger('size')->nullable();
            
            // PER IMMAGINI (opzionale ma comodo)
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            
            // ORDINAMENTO
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_primary')->default(false);
            
            $table->timestamps();
            
            // Indici per performance
            $table->index(['model_id', 'model_type', 'collection']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};