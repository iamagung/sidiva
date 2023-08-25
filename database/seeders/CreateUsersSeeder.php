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
            'level'=> '1',
            'password'=> bcrypt('admin'),
        ]);
        User::create([
            'name'=>'Admin MCU',
            'username' => 'adminmcu',
            'email'=> 'adminmcu@example.com',
            'level'=> '2',
            'password'=> bcrypt('adminmcu'),
        ]);
        User::create([
            'name'=>'Admin Homecare',
            'username' => 'adminhc',
            'email'=> 'adminhomecare@example.com',
            'level'=> '3',
            'password'=> bcrypt('adminhc'),
        ]);
        User::create([
            'name'=>'Admin PSC',
            'username' => 'adminpsc',
            'email'=> 'adminpsc@example.com',
            'level'=> '4',
            'password'=> bcrypt('adminpsc'),
        ]);
    }
}
