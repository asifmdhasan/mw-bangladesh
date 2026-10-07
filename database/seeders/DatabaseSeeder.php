<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(['email' => 'admin@example.com'], [
            'name' => 'MW Admin', 'role' => 'admin', 'password' => Hash::make('password123'),
        ]);

        $this->call(CategorySeeder::class);

        $stories = [
            ['Culture', 'culture', 'The new Dhaka sound is being built one small room at a time', 'Independent musicians are turning intimate listening rooms into the city’s most exciting cultural spaces.', 'A new generation of independent musicians is building a scene on its own terms. Across the city, small listening rooms and late-night sessions are bringing strangers together around fresh sounds.\n\nThe rooms may be modest, but the ambition is not. Artists are experimenting with local instruments, electronic textures and stories rooted in everyday life. For audiences, the appeal is simple: discovery, closeness and a night that feels like it belongs to the city.'],
            ['Style', 'style', 'A quieter kind of tailoring is finding its place in the city', 'Meet the designers making everyday dressing feel considered, comfortable and unmistakably local.', 'Good style does not need to announce itself. A new wave of Dhaka designers is focusing on thoughtful cuts, breathable fabrics and versatile pieces that move easily from work to the weekend.\n\nTheir approach starts with listening to how people actually live. The result is clothing with a sense of occasion that never gets in the way of comfort.'],
            ['Grooming', 'grooming', 'The five-minute routine that makes mornings feel easier', 'A practical guide to a simple, consistent grooming routine built around a few essentials.', 'A better morning routine is the one you can keep. Start with a gentle cleanser, follow with a lightweight moisturiser and make SPF a daily habit.\n\nKeep products where you use them and give a new routine a little time. Consistency does more work than a crowded shelf.'],
            ['Watches', 'watches', 'Mechanical watches, and the pleasure of slowing down', 'Why a small, well-made object still holds attention in a world that moves at screen speed.', 'A mechanical watch is a tiny machine with a generous point of view. Its movement is visible, its purpose clear and its pace refreshingly human.\n\nCollectors often start with curiosity rather than investment. They learn how a case wears, how a dial catches light and why an object made to last can feel personal.'],
            ['Living', 'living', 'A home that makes room for the way you really live', 'Design notes for a calmer room: natural light, flexible furniture and details that earn their place.', 'The best rooms leave space for real life. Designers are choosing flexible furniture, warm materials and a few personal objects instead of filling every corner.\n\nBegin with the light and the way you move through the room. A small change to a chair, lamp or shelf can shift the whole feeling of a space.'],
            ['Travel', 'travel', 'A slow weekend along the river, just beyond the city', 'An easy two-day escape built around early walks, local food and time by the water.', 'Leave early, travel light and give the day room to unfold. A riverside weekend can be as simple as a walk before the heat, a long lunch and an unhurried stretch by the water.\n\nAsk local hosts what is in season and keep the schedule flexible. The best part is often the hour you did not plan.'],
        ];
        $editorialSections = [
            ['Style', 'style', [
                'From desk to dinner: a more versatile Dhaka wardrobe', 'Five pieces that quietly do more', 'Why a good fit changes everything',
                'The new voices reshaping local menswear', 'A practical guide to wearing colour', 'Finding the right everyday shoes',
            ]],
            ['Entertainment', 'entertainment', [
                'Independent cinema finds a home beyond the festival', 'The rooms making live music feel close again', 'Meet the next generation of Dhaka storytellers',
                'Why the city is falling for the short film', 'One stage, many new voices', 'A soundtrack for a changing city',
                'The new series making family dinner a debate',
            ]],
            ['Magazine', 'magazine', [
                'A conversation about art, empathy and everyday life', 'The makers preserving a changing craft', 'Inside a studio built for the next idea',
                'The objects we keep, and the stories they hold', 'A table set with the flavours of home', 'Portraits of a city in motion',
                'The thoughtful guide to collecting well',
            ]],
        ];

        foreach ($editorialSections as [$name, $slug, $titles]) {
            foreach ($titles as $title) {
                $excerpt = 'A fresh perspective on '.strtolower($name).' from the people, places and ideas shaping life in Bangladesh.';
                $body = $excerpt."\n\nOur editors went looking for the small details behind the bigger story. What they found is a portrait of people making considered choices, sharing ideas and building something with care.\n\nThis is an original MW Bangladesh feature, created for readers who like to look a little closer.";
                $stories[] = [$name, $slug, $title, $excerpt, $body];
            }
        }

        $photos = ['1516280440614-37939bbacd81', '1490481651871-ab68de25d43d', '1621607512022-6aecc4fed814', '1523170335258-f5ed11844a49', '1600210492486-724fe5c67fb0', '1500530855697-b586d89ba3ee', '1515886657613-9f3515b0c78f', '1497366754035-f200968a6e72', '1513364776144-60967b0f800f', '1517457373958-b7bdd4587205', '1511988617509-a57c8a288659', '1529139574466-a303027c1d8b'];

        foreach ($stories as $index => [$name, $slug, $title, $excerpt, $body]) {
            $body = str_replace('\\n', "\n", $body);
            $category = Category::updateOrCreate(['slug' => $slug], ['name' => $name, 'description' => 'Ideas and perspectives on '.strtolower($name).'.']);
            $article = Article::updateOrCreate(['slug' => $slug.'-'.($index + 1)], [
                'category_id' => $category->id, 'author_id' => $admin->id, 'title' => $title,
                'excerpt' => $excerpt, 'body' => $body,
                'image_url' => 'https://images.unsplash.com/photo-'.$photos[$index % count($photos)].'?auto=format&fit=crop&w=1400&q=85',
                'is_featured' => $index === 0, 'is_spotlight' => $index < 7, 'published_at' => now()->subDays($index + 1),
            ]);
            $article->categories()->sync([$category->id]);
        }

        $this->call(DemoSectionsSeeder::class);
    }
}
