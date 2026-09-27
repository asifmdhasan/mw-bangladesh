<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->after('id')->constrained('categories')->nullOnDelete();
            $table->unique(['parent_id', 'name'], 'categories_parent_name_unique');
        });

        if (Schema::hasTable('category_hierarchy')) {
            DB::table('category_hierarchy')->select('child_id', DB::raw('MIN(parent_id) as parent_id'))
                ->groupBy('child_id')->orderBy('child_id')->get()
                ->each(fn ($relation) => DB::table('categories')->where('id', $relation->child_id)->update(['parent_id' => $relation->parent_id]));

            Schema::drop('category_hierarchy');
        }

        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement('CREATE UNIQUE INDEX categories_root_name_unique ON categories ((COALESCE(parent_id, 0)), name)');
        } elseif ($driver === 'sqlsrv') {
            DB::statement('CREATE UNIQUE INDEX categories_root_name_unique ON categories (name) WHERE parent_id IS NULL');
        } else {
            DB::statement('CREATE UNIQUE INDEX categories_root_name_unique ON categories (name) WHERE parent_id IS NULL');
        }
    }

    public function down(): void
    {
        Schema::create('category_hierarchy', function (Blueprint $table) {
            $table->foreignId('parent_id')->constrained('categories')->cascadeOnDelete();
            $table->foreignId('child_id')->constrained('categories')->cascadeOnDelete();
            $table->primary(['parent_id', 'child_id']);
            $table->index('child_id');
        });
        DB::table('categories')->whereNotNull('parent_id')->orderBy('id')->get(['id', 'parent_id'])->each(
            fn ($category) => DB::table('category_hierarchy')->insert(['parent_id' => $category->parent_id, 'child_id' => $category->id])
        );

        if (DB::getDriverName() === 'mysql' || DB::getDriverName() === 'sqlsrv') {
            DB::statement('DROP INDEX categories_root_name_unique ON categories');
        } else {
            DB::statement('DROP INDEX categories_root_name_unique');
        }
        Schema::table('categories', function (Blueprint $table) {
            $table->dropUnique('categories_parent_name_unique');
            $table->dropConstrainedForeignId('parent_id');
        });
    }
};
