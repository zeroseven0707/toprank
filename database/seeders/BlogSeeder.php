<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user =
            User::first() ??
            User::factory()->create([
                'name' => 'Admin',
                'email' => 'admin@example.com',
            ]);

        $categories = ['Technology', 'Lifestyle', 'Travel', 'Business', 'Food'];
        $tags = [['tech', 'innovation', 'ai'], ['health', 'life', 'mindset'], ['adventure', 'nature', 'culture'], ['marketing', 'startup', 'growth'], ['recipe', 'culinary', 'kitchen']];

        for ($i = 0; $i < 10; $i++) {
            $title = fake()->sentence(6);
            $categoryIndex = $i % count($categories);

            Blog::create([
                'user_id' => $user->id,
                'title' => $title,
                'slug' => Str::slug($title) . '-' . $i,
                'excerpt' => fake()->paragraph(),
                'content' => $this->generateContent(),
                'image' => "https://source.unsplash.com/800x600/?{$categories[$categoryIndex]},blog",
                'category' => $categories[$categoryIndex],
                'tags' => json_encode($tags[$categoryIndex]),
                'status' => 'published',
            ]);
        }
    }

    private function generateContent(): string
    {
        $paragraphs = fake()->paragraphs(rand(4, 8));
        $html = '';

        foreach ($paragraphs as $p) {
            $html .= "<p>{$p}</p>\n";
        }

        $html .= '<h2>Subheading Example</h2>';
        $html .= '<p>This is an example of a subheading with extra content to show typography and layout. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>';

        $html .= '<ul><li>First point</li><li>Second point</li><li>Third point</li></ul>';

        return $html;
    }
}
