<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    private const HIERARCHY = [
        'Magazine' => ['Cover Story', 'From the Magazine', 'Web Special'],
        'Entertainment' => ['Binge', 'Cinema'],
        'Culture' => ['Art', 'Astrology', 'Books', 'Features', 'Music', 'Pop Culture'],
        'Experiences' => ['Destination', 'Food & Drink', 'Heritage', 'Travel'],
        'Health & Fitness' => ['Fitness', 'Wellness'],
        'People' => ['The Interview', 'Life Lessons', 'Radar', 'Talk'],
        'Style & Luxury' => ['Decor', 'Fashion', 'Grooming Special', 'The Brand', 'Watches'],
        'Sports' => ['Cricket', 'Football'],
        'Tech' => ['Gadgets'],
        'Wheels' => ['Auto', 'Car & Bike Special'],
        'More' => ['Fast Forward'],
        'Events' => ['Dhaka Diorama'],
    ];

    public function run(): void
    {
        DB::transaction(function (): void {
            $parents = [];

            foreach (array_keys(self::HIERARCHY) as $name) {
                $parents[$name] = $this->saveCategory($name, null);
            }

            foreach (self::HIERARCHY as $parentName => $children) {
                foreach ($children as $name) {
                    $this->saveCategory($name, $parents[$parentName]);
                }
            }
        });
    }

    private function saveCategory(string $name, ?Category $parent): Category
    {
        $parentId = $parent?->id;
        $category = Category::where('name', $name)->where('parent_id', $parentId)->first();

        // Reuse existing root categories when the requested structure moves them under a parent.
        if (! $category && $parent) {
            $category = Category::where('name', $name)->whereNull('parent_id')->first();
        }

        $category ??= new Category();
        $category->name = $name;
        $category->parent_id = $parentId;
        $category->slug = Category::uniqueSlug($name, $category->exists ? (int) $category->id : null);
        $category->save();

        return $category;
    }
}
