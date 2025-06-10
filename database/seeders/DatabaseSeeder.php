<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\TaskCategory;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = User::all();
        if ($users->isEmpty()) {
            $user = User::create([
                'name' => 'Default User',
                'email' => 'default@example.com',
                'password' => bcrypt('password'),
            ]);
        } else {
            $user = $users->first();
        }

        TaskStatus::firstOrCreate(['name' => 'pending']);
        TaskStatus::firstOrCreate(['name' => 'inprogress']);
        TaskStatus::firstOrCreate(['name' => 'completed']);

        TaskCategory::firstOrCreate(['name' => 'work']);
        TaskCategory::firstOrCreate(['name' => 'personal']);
        TaskCategory::firstOrCreate(['name' => 'urgent']);

        Task::factory()->count(10)->create([
            'user_id' => $user->id,
            'status_id' => 1,
            'category_id' => 1,
        ]);
        Task::factory()->count(10)->create([
            'user_id' => $user->id,
            'status_id' => 2,
            'category_id' => 2,
        ]);
        Task::factory()->count(10)->create([
            'user_id' => $user->id,
            'status_id' => 3,
            'category_id' => 3,
        ]);

    }
}
