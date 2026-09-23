<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoSectionsSeeder extends Seeder
{
    public function run(): void
    {
        $sections = [
            'style' => [
                'name' => 'Style',
                'description' => 'Personal style, design and new voices in Bangladeshi menswear.',
                'titles' => [
                    'From desk to dinner: a more versatile Dhaka wardrobe',
                    'Five pieces that quietly do more',
                    'Why a good fit changes everything',
                    'The new voices reshaping local menswear',
                    'A practical guide to wearing colour',
                    'Finding the right everyday shoes',
                    'A closer look at modern occasion dressing',
                ],
            ],
            'entertainment' => [
                'name' => 'Entertainment',
                'description' => 'Film, music and the people making culture move.',
                'titles' => [
                    'Independent cinema finds a home beyond the festival',
                    'The rooms making live music feel close again',
                    'Meet the next generation of Dhaka storytellers',
                    'Why the city is falling for the short film',
                    'One stage, many new voices',
                    'A soundtrack for a changing city',
                    'The new series making family dinner a debate',
                ],
            ],
            'magazine' => [
                'name' => 'Magazine',
                'description' => 'Long reads, conversations and ideas from across Bangladesh.',
                'titles' => [
                    'A conversation about art, empathy and everyday life',
                    'The makers preserving a changing craft',
                    'Inside a studio built for the next idea',
                    'The objects we keep, and the stories they hold',
                    'A table set with the flavours of home',
                    'Portraits of a city in motion',
                    'The thoughtful guide to collecting well',
                ],
            ],
        ];
        $photos = [
            '1515886657613-9f3515b0c78f', '1497366754035-f200968a6e72', '1513364776144-60967b0f800f',
            '1517457373958-b7bdd4587205', '1511988617509-a57c8a288659', '1529139574466-a303027c1d8b',
            '1516280440614-37939bbacd81', '1506157786151-b8491531f063', '1514525253161-7a46d19cd819',
            '1485846234645-a62644f84728', '1517604931442-7e0c8ed2963c', '1470229722913-7c0e2dbbafd3',
            '1513364776144-60967b0f800f', '1494438639946-1ebd1d20bf85', '1500530855697-b586d89ba3ee',
            '1519682337058-a94d519337bc', '1513519245088-0e12902e5a38', '1517248135467-4c7edcad34c4',
            '1497366216548-37526070297c', '1497366811353-6870744d04b2', '1517457373958-b7bdd4587205',
        ];
        $authorId = User::where('role', 'admin')->orderBy('id')->value('id');

        foreach ($sections as $slug => $section) {
            $category = Category::firstOrCreate(['slug' => $slug], [
                'name' => $section['name'],
                'description' => $section['description'],
            ]);

            foreach ($section['titles'] as $index => $title) {
                $excerpt = 'A fresh perspective on '.strtolower($section['name']).', told through the people, places and ideas shaping life in Bangladesh.';
                $articleSlug = $slug.'-feature-'.str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT);

                $article = Article::updateOrCreate(['slug' => $articleSlug], [
                    'category_id' => $category->id,
                    'author_id' => $authorId,
                    'title' => $title,
                    'excerpt' => $excerpt,
                    'body' => $excerpt."\n\nOur editors went looking for the small details behind the bigger story. What they found is a portrait of people making considered choices, sharing ideas and building something with care.\n\nThis is an original Man’s World Bangladesh feature, created for readers who like to look a little closer.",
                    'image_url' => 'https://images.unsplash.com/photo-'.$photos[($index + array_search($slug, array_keys($sections), true) * 7) % count($photos)].'?auto=format&fit=crop&w=1400&q=85',
                    'is_featured' => false,
                    'is_spotlight' => $slug === 'style',
                    'published_at' => now()->subHours($index + 1 + (array_search($slug, array_keys($sections), true) * 24)),
                ]);
                $article->categories()->sync([$category->id]);
            }
        }
    }
}
