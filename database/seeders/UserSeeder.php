<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = new \App\Models\User; // Adjust the namespace if necessary
        $user->name = 'admin';
        $user->email = 'admin@example.com'; // Use a valid email address
        $user->password = bcrypt('213eujfuir3edsjfsDD');
        $user->is_admin = 1;
        $user->save();
    }
}
