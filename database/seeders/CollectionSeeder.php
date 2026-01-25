<?php

namespace Database\Seeders;

use App\Models\Collection;
use App\Models\Bookmark;
use App\Models\User;
use Illuminate\Database\Seeder;

class CollectionSeeder extends Seeder
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

        $collectionNames = [
            'Web Development',
            'Design Resources',
            'Learning Materials',
            'Tools & Utilities',
            'Favorites',
            'Work Projects',
            'Personal',
            'Tutorials',
            'Documentation',
            'News & Updates',
        ];

        foreach ($users as $user) {
            // Each user gets 3-5 collections
            $collectionsForUser = collect($collectionNames)
                ->random(rand(3, 5))
                ->map(function ($collectionName) use ($user) {
                    return Collection::create([
                        'name' => $collectionName,
                        'user_id' => $user->id,
                    ]);
                });

            // Add bookmarks to collections
            $userBookmarks = Bookmark::where('user_id', $user->id)->get();

            if ($userBookmarks->isNotEmpty()) {
                foreach ($collectionsForUser as $collection) {
                    // Each collection gets 2-5 random bookmarks
                    $randomBookmarks = $userBookmarks->random(
                        rand(2, min(5, $userBookmarks->count()))
                    );
                    $collection->bookmarks()->attach($randomBookmarks->pluck('id'));
                }
            }
        }

        $this->command->info('Collections seeded successfully!');
    }
}

