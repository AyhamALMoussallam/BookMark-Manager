<?php

namespace Database\Seeders;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
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

        $commonTags = [
            'Web Development',
            'Design',
            'Programming',
            'Tutorial',
            'Documentation',
            'Tools',
            'Productivity',
            'Learning',
            'News',
            'Blog',
            'JavaScript',
            'PHP',
            'Laravel',
            'React',
            'Vue.js',
            'CSS',
            'HTML',
            'API',
            'Database',
            'Security',
        ];

        foreach ($users as $user) {
            // Each user gets a random subset of tags (5-10 tags)
            $tagsForUser = collect($commonTags)
                ->random(rand(5, 10))
                ->map(function ($tagName) use ($user) {
                    return [
                        'name' => $tagName,
                        'user_id' => $user->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })
                ->toArray();

            Tag::insert($tagsForUser);
        }

        $this->command->info('Tags seeded successfully!');
    }
}

