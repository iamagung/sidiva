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
            'lihat_password'=> 'admin',
            'telepon'=> '08100000000000'
        ]);
        User::create([
            'name'=>'Admin MCU',
            'username' => 'adminmcu',
            'email'=> 'adminmcu@example.com',
            'level'=> 'adminmcu',
            'password'=> bcrypt('adminmcu'),
            'lihat_password'=> 'adminmcu',
            'telepon'=> '08100000000000'
        ]);
        User::create([
            'name'=>'Admin Homecare',
            'username' => 'adminhomecare',
            'email'=> 'adminhomecare@example.com',
            'level'=> 'adminhomecare',
            'password'=> bcrypt('adminhomecare'),
            'lihat_password'=> 'adminhomecare',
            'telepon'=> '08100000000000'
        ]);
        User::create([
            'name'=>'Admin Telemedicine',
            'username' => 'admintelemedis',
            'email'=> 'admintelemedis@example.com',
            'level'=> 'admintelemedis',
            'password'=> bcrypt('admintelemedis'),
            'lihat_password'=> 'admintelemedis',
            'telepon'=> '08100000000000'
        ]);
    }
}
