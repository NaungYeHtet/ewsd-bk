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
            $table->foreignIdFor(\App\Models\Department::class)->constrained('staffs');
            $table->foreignIdFor(\App\Models\Staff::class)->constrained('staffs');
            $table->foreignIdFor(\App\Models\Academic::class)->constrained('academics', 'uuid');

            $table->string('slug')->unique();
            $table->string('title')->index();
            $table->text('content');
            $table->string('file')->nullable();
            // $table->json('reactions_count')->nullable();
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
