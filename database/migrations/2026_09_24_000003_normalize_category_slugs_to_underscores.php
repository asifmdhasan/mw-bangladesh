<?php

use App\Models\Category;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void { $this->rewriteSlugs('_'); }
    public function down(): void { $this->rewriteSlugs('-'); }

    private function rewriteSlugs(string $separator): void
    {
        DB::table('categories')->orderBy('id')->get(['id'])->each(function ($category) {
            DB::table('categories')->where('id', $category->id)->update(['slug' => '__category_migration_'.$category->id.'_'.Str::random(8)]);
        });
        $used = [];
        foreach (Category::orderBy('id')->get(['id', 'name']) as $category) {
            $base = Str::slug($category->name, $separator) ?: 'category';
            $slug = $base;
            $suffix = 2;
            while (isset($used[$slug])) $slug = $base.$separator.$suffix++;
            $used[$slug] = true;
            DB::table('categories')->where('id', $category->id)->update(['slug' => $slug]);
        }
    }
};
