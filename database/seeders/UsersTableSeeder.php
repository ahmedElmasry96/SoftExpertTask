<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'manager 1',
            'email' => 'manager1@manager.com',
            'password' => bcrypt('manager1'),
            'role' => 'manager',
        ]);

        User::create([
            'name' => 'manager 2',
            'email' => 'manager2@manager.com',
            'password' => bcrypt('manager2'),
            'role' => 'manager',
        ]);

        User::create([
            'name' => 'user 1',
            'email' => 'user1@user.com',
            'password' => bcrypt('user1'),
        ]);

        User::create([
            'name' => 'user 2',
            'email' => 'user2@user.com',
            'password' => bcrypt('user2'),
        ]);

        User::create([
            'name' => 'user 3',
            'email' => 'user3@user.com',
            'password' => bcrypt('user3'),
        ]);
    }
}
