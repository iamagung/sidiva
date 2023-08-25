<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class CreateUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name'=>'Admin',
            'username' => 'admin',
            'email'=> 'admin@example.com',
            'level'=> 'admin',
            'password'=> bcrypt('admin'),
        ]);
    }
}
