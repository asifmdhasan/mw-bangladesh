<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id(); $table->string('name'); $table->string('slug')->unique(); $table->text('description')->nullable(); $table->timestamps();
        });
        Schema::create('articles', function (Blueprint $table) {
            $table->id(); $table->foreignId('category_id')->constrained()->cascadeOnDelete(); $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title'); $table->string('slug')->unique(); $table->string('excerpt', 320); $table->longText('body'); $table->string('image_url', 500)->nullable();
            $table->boolean('is_featured')->default(false); $table->timestamp('published_at')->nullable(); $table->timestamps();
        });
        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id(); $table->string('email')->unique(); $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_subscribers'); Schema::dropIfExists('articles'); Schema::dropIfExists('categories');
    }
};
