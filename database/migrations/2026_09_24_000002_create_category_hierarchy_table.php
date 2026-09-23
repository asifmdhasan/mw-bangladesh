<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('category_hierarchy', function (Blueprint $table) {
            $table->foreignId('parent_id')->constrained('categories')->cascadeOnDelete();
            $table->foreignId('child_id')->constrained('categories')->cascadeOnDelete();
            $table->primary(['parent_id', 'child_id']);
            $table->index('child_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_hierarchy');
    }
};
