<?php

use Illuminate\Database\Seeder;

use App\Database\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $user = User::create([
        'name' => 'Administrator',
        'username' => 'admin',
        'email' => 'test@email.com',
        'password' => Hash::make('test123')
      ]);
      $user->save();

      // $this->call(UsersTableSeeder::class);

    }
}
