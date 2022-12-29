<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name'      => 'admin',
            'username'  => 'admin',
            'email'     => 'admin@gmail.com',
            'password'  => 'admin',
            'api_token' => Str::random(60),
            'roles'     => 'ADMIN'
        ]);
        User::create([
            'name'      => 'redaktur',
            'username'  => 'redaktur',
            'email'     => 'redaktur@gmail.com',
            'password'  => 'redaktur',
            'api_token' => Str::random(60),
            'roles'     => 'REDAKTUR'
        ]);
        User::create([
            'name'      => 'reporter',
            'username'  => 'reporter',
            'email'     => 'reporter@gmail.com',
            'password'  => 'reporter',
            'api_token' => Str::random(60),
            'roles'     => 'REPORTER'
        ]);
    }
}
