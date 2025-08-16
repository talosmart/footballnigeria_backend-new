<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Category;
use App\Models\Post;
use Carbon\Carbon;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create 5 blog categories
        $categories = [
            'Premier League',
            'La Liga',
            'Serie A',
            'Bundesliga',
            'CAF Champions League',
        ];
        $categoryIds = [];
        foreach ($categories as $cat) {
            $category = Category::create([
                'name' => $cat,
                'slug' => Str::slug($cat),
                'description' => 'All about ' . $cat . ' football news, updates, and analysis.',
            ]);
            $categoryIds[] = $category->id;
        }

        // 2. Get 5 random users as authors
        $authors = User::inRandomOrder()->limit(5)->pluck('id')->toArray();

        // 3. Create 20 blog posts
        $featuredImages = [
            'https://images.unsplash.com/photo-1506744038136-46273834b3fb',
            'https://images.unsplash.com/photo-1517649763962-0c623066013b',
            'https://images.unsplash.com/photo-1464983953574-0892a716854b',
            'https://images.unsplash.com/photo-1518972559570-1ec7facb3e41',
            'https://images.unsplash.com/photo-1505843273132-bc5c6f7b5b8a',
            'https://images.unsplash.com/photo-1465101046530-73398c7f28ca',
            'https://images.unsplash.com/photo-1517649763962-0c623066013b',
            'https://images.unsplash.com/photo-1464983953574-0892a716854b',
            'https://images.unsplash.com/photo-1518972559570-1ec7facb3e41',
            'https://images.unsplash.com/photo-1505843273132-bc5c6f7b5b8a',
            'https://images.unsplash.com/photo-1465101046530-73398c7f28ca',
            'https://images.unsplash.com/photo-1506744038136-46273834b3fb',
            'https://images.unsplash.com/photo-1517649763962-0c623066013b',
            'https://images.unsplash.com/photo-1464983953574-0892a716854b',
            'https://images.unsplash.com/photo-1518972559570-1ec7facb3e41',
            'https://images.unsplash.com/photo-1505843273132-bc5c6f7b5b8a',
            'https://images.unsplash.com/photo-1465101046530-73398c7f28ca',
            'https://images.unsplash.com/photo-1506744038136-46273834b3fb',
            'https://images.unsplash.com/photo-1517649763962-0c623066013b',
            'https://images.unsplash.com/photo-1464983953574-0892a716854b',
        ];

        $titles = [
            'Top 10 Nigerian Footballers of All Time',
            'How VAR is Changing the Game in Africa',
            'The Rise of Women’s Football in Nigeria',
            'Super Eagles: Road to the Next AFCON',
            'Grassroots Football Development in Lagos',
            'The Impact of European Leagues on Nigerian Talent',
            'Best Football Stadiums in Nigeria',
            'How to Spot the Next Football Star',
            'The Business of Football in Africa',
            'Legendary Coaches in Nigerian Football',
            'Why Nigerian Fans Love the Premier League',
            'The Science Behind Football Training',
            'Football and Education: Balancing Both',
            'The Most Memorable World Cup Moments',
            'How Social Media is Shaping Football Culture',
            'The Future of Nigerian Football Academies',
            'Football Injuries: Prevention and Recovery',
            'The Role of Fans in Football Success',
            'How to Become a Professional Footballer in Nigeria',
            'The Evolution of Football Kits and Gear',
        ];

        $contents = [
            'Football in Nigeria has produced some of the greatest talents in Africa. This article explores the top 10 Nigerian footballers who have made a mark both locally and internationally.',
            'Video Assistant Referee (VAR) technology is revolutionizing football in Africa. We look at its impact on the game, controversies, and future prospects.',
            'Women’s football is on the rise in Nigeria, with more girls taking up the sport and clubs investing in female teams. Here’s how the landscape is changing.',
            'The Super Eagles are preparing for the next AFCON. We analyze their chances, key players, and what fans can expect.',
            'Grassroots football in Lagos is booming, with academies and local clubs nurturing the next generation of stars.',
            'European leagues have a huge influence on Nigerian footballers. We discuss how this exposure is shaping talent and opportunities.',
            'From Abuja to Lagos, Nigeria boasts some impressive football stadiums. Here’s our pick of the best venues.',
            'Scouting young talent is crucial for football development. Learn how to spot the next big star in Nigerian football.',
            'Football is big business in Africa. We break down the economics, sponsorships, and investments driving the sport.',
            'Nigeria has produced legendary football coaches. We celebrate their achievements and contributions to the game.',
            'The Premier League is hugely popular among Nigerian fans. We explore the reasons behind this passion.',
            'Modern football training uses science and technology to improve performance. Here’s how Nigerian clubs are adopting new methods.',
            'Balancing football and education is a challenge for young players. We offer tips and stories from those who have succeeded.',
            'World Cup moments that made history for Nigeria and Africa. Relive the excitement and drama.',
            'Social media is changing how fans and players interact. We look at its impact on football culture in Nigeria.',
            'Football academies are the future of Nigerian football. Discover how they are shaping tomorrow’s stars.',
            'Injuries are part of the game. Learn about prevention, treatment, and recovery for footballers.',
            'Fans play a vital role in football success. We highlight their influence on teams and matches.',
            'Dreaming of going pro? Here’s a step-by-step guide to becoming a professional footballer in Nigeria.',
            'Football kits and gear have evolved over the years. See the latest trends and innovations.',
        ];

        for ($i = 0; $i < 20; $i++) {
            Post::create([
                'title' => $titles[$i],
                'content' => $contents[$i],
                'category_id' => $categoryIds[array_rand($categoryIds)],
                'excerpt' => Str::limit($contents[$i], 120),
                'featured_image' => 'baller.jpg',
                'slug' => Str::slug($titles[$i]) . '-' . ($i+1),
                'published_at' => Carbon::now()->subDays(rand(0, 60)),
                'is_featured' => rand(0,1),
                'is_trending' => rand(0,1),
                'is_featured_video' => false,
                'author_id' => $authors[array_rand($authors)],
            ]);
        }
    }
}
