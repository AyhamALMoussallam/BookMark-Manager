<?php

namespace Database\Seeders;

use App\Models\Bookmark;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookmarkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Please run UserSeeder first.');
            return;
        }

        $sampleBookmarks = [
            [
                'url' => 'https://laravel.com',
                'title' => 'Laravel - The PHP Framework',
                'description' => 'Laravel is a web application framework with expressive, elegant syntax.',
                'is_favorite' => true,
            ],
            [
                'url' => 'https://react.dev',
                'title' => 'React - The Library for Web and Native User Interfaces',
                'description' => 'React lets you build user interfaces out of individual pieces called components.',
                'is_favorite' => true,
            ],
            [
                'url' => 'https://vuejs.org',
                'title' => 'Vue.js - The Progressive JavaScript Framework',
                'description' => 'Vue.js is an approachable, performant and versatile framework for building web user interfaces.',
                'is_favorite' => false,
            ],
            [
                'url' => 'https://github.com',
                'title' => 'GitHub',
                'description' => 'The world\'s leading software development platform.',
                'is_favorite' => true,
            ],
            [
                'url' => 'https://stackoverflow.com',
                'title' => 'Stack Overflow',
                'description' => 'Where developers learn, share, and build careers.',
                'is_favorite' => false,
            ],
            [
                'url' => 'https://developer.mozilla.org',
                'title' => 'MDN Web Docs',
                'description' => 'Resources for developers, by developers.',
                'is_favorite' => true,
            ],
            [
                'url' => 'https://www.php.net',
                'title' => 'PHP: Hypertext Preprocessor',
                'description' => 'Official PHP documentation and resources.',
                'is_favorite' => false,
            ],
            [
                'url' => 'https://tailwindcss.com',
                'title' => 'Tailwind CSS',
                'description' => 'A utility-first CSS framework for rapidly building custom designs.',
                'is_favorite' => false,
            ],
            [
                'url' => 'https://www.postgresql.org',
                'title' => 'PostgreSQL',
                'description' => 'The world\'s most advanced open source relational database.',
                'is_favorite' => false,
            ],
            [
                'url' => 'https://redis.io',
                'title' => 'Redis',
                'description' => 'The open source, in-memory data store used by millions of developers.',
                'is_favorite' => false,
            ],
            [
                'url' => 'https://www.docker.com',
                'title' => 'Docker',
                'description' => 'Empowering App Development for Developers.',
                'is_favorite' => false,
            ],
            [
                'url' => 'https://kubernetes.io',
                'title' => 'Kubernetes',
                'description' => 'Production-grade container orchestration.',
                'is_favorite' => false,
            ],
            [
                'url' => 'https://www.figma.com',
                'title' => 'Figma',
                'description' => 'The collaborative interface design tool.',
                'is_favorite' => false,
            ],
            [
                'url' => 'https://www.adobe.com/products/photoshop.html',
                'title' => 'Adobe Photoshop',
                'description' => 'Create and enhance your photographs, images, and designs.',
                'is_favorite' => false,
            ],
            [
                'url' => 'https://www.youtube.com',
                'title' => 'YouTube',
                'description' => 'Enjoy the videos and music you love.',
                'is_favorite' => false,
            ],
        ];

        foreach ($users as $user) {
            // Each user gets 8-12 random bookmarks
            $bookmarksForUser = collect($sampleBookmarks)
                ->random(rand(8, 12))
                ->map(function ($bookmarkData) use ($user) {
                    $domain = parse_url($bookmarkData['url'], PHP_URL_HOST);
                    $faviconUrl = "https://www.google.com/s2/favicons?domain={$domain}&sz=64";

                    return Bookmark::create([
                        'user_id' => $user->id,
                        'url' => $bookmarkData['url'],
                        'title' => $bookmarkData['title'],
                        'description' => $bookmarkData['description'],
                        'favicon_url' => $faviconUrl,
                        'is_favorite' => $bookmarkData['is_favorite'] ?? false,
                    ]);
                });

            // Attach random tags to each bookmark (1-4 tags per bookmark)
            $userTags = Tag::where('user_id', $user->id)->get();

            if ($userTags->isNotEmpty()) {
                foreach ($bookmarksForUser as $bookmark) {
                    $randomTags = $userTags->random(rand(1, min(4, $userTags->count())));
                    $bookmark->tags()->attach($randomTags->pluck('id'));
                }
            }
        }

        $this->command->info('Bookmarks seeded successfully!');
    }
}

