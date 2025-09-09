<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Level;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create initial admin user
        User::query()->updateOrCreate(
            ['email' => 'admin@logic-labyrinth.local'],
            [
                'name' => 'Admin',
                'password' => bcrypt('admin123'), // please change in production
                'is_admin' => true,
            ]
        );

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('user1234'),
        ]);

        // Seed 3 easy and 3 hard levels (dummy code snippets for demo)
        $easyLevels = [
            [
                'code' => "// Easy 1\nmoveRight();moveRight();moveDown();",
                'correct' => ['x' => 2, 'y' => 1],
            ],
            [
                'code' => "// Easy 2\nmoveDown();moveDown();moveRight();",
                'correct' => ['x' => 1, 'y' => 2],
            ],
            [
                'code' => "// Easy 3\nmoveRight();moveDown();moveRight();",
                'correct' => ['x' => 2, 'y' => 1],
            ],
        ];

        $hardLevels = [
            [
                'code' => "// Hard 1\nmoveRight();moveRight();moveRight();moveDown();moveLeft();",
                'correct' => ['x' => 2, 'y' => 1],
            ],
            [
                'code' => "// Hard 2\nmoveDown();moveRight();moveDown();moveRight();moveUp();",
                'correct' => ['x' => 2, 'y' => 1],
            ],
            [
                'code' => "// Hard 3\nmoveDown();moveDown();moveRight();moveUp();moveRight();",
                'correct' => ['x' => 2, 'y' => 1],
            ],
        ];

        foreach ($easyLevels as $l) {
            Level::updateOrCreate(
                ['code' => $l['code']],
                [
                    'difficulty' => 'easy',
                    'correct_answer' => $l['correct'],
                ]
            );
        }

        foreach ($hardLevels as $l) {
            Level::updateOrCreate(
                ['code' => $l['code']],
                [
                    'difficulty' => 'hard',
                    'correct_answer' => $l['correct'],
                ]
            );
        }
    }
}
