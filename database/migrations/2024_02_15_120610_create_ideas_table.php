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
        Schema::create('ideas', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Admin::class)->constrained()->cascadeOnDelete();

            $table->string('slug')->unique();
            $table->string('title');
            $table->text('content');
            $table->json('files')->nullable();
            $table->boolean('is_anonymous')->default(false);
            
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ideas');
    }
};
