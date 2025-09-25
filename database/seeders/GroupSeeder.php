<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Group;
use App\Models\User;


class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::all()->each(function (User $user) {
            if ($user->groups()->exists()) {
                return;
            }
            $groups = [
                [
                    'user_id' => $user->id,
                    'name' => 'General',
                    'description' => 'Default group for all contacts',
                    'color' => '#6366f1',
                    'is_default' => true,
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'Family',
                    'description' => 'Family members and relatives',
                    'color' => '#ef4444',
                    'is_default' => false,
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'Work',
                    'description' => 'Professional contacts',
                    'color' => '#10b981',
                    'is_default' => false,
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'Friends',
                    'description' => 'Personal friends',
                    'color' => '#f59e0b',
                    'is_default' => false,
                ],
            ];
            Group::insert($groups);
        });
    }
}
