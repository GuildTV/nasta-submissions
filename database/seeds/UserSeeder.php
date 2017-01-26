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
      User::create([
        'name' => 'Test Admin',
        'compact_name' => "TestAdmin",
        'username' => 'test_admin',
        'email' => 'test@email.com',
        'type' => 'admin',
        'password' => Hash::make('test123')
      ]);

      User::create([
        'name' => 'Test Judge',
        'compact_name' => "TestJudge",
        'username' => 'test_judge',
        'email' => 'judge@email.com',
        'type' => 'judge',
        'password' => Hash::make('test123')
      ]);

      $this->call(StationsSeeder::class);
    }
}
