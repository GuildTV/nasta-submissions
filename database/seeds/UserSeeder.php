<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

use App\Database\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $user = User::create([
        'name' => 'Test Admin',
        'username' => 'test_admin',
        'email' => 'test@email.com',
        'type' => 'admin',
        'password' => Hash::make('test123')
      ]);
      $user->save();
      $user = User::create([
        'name' => 'Test Judge',
        'username' => 'test_judge',
        'email' => 'judge@email.com',
        'type' => 'judge',
        'password' => Hash::make('test123')
      ]);
      $user->save();
      $user = User::create([
        'name' => 'Test Station',
        'username' => 'test_station',
        'email' => 'station@email.com',
        'type' => 'station',
        'password' => Hash::make('test123')
      ]);
      $user->save();
    }
}
