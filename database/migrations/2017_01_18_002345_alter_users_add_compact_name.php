<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUsersAddCompactName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('compact_name')->after('name');
        });

        $users = DB::table('users')->get();
        foreach($users as $user){
            DB::table('users')
                ->where('id', $user->id)
                ->update([ 'compact_name' => str_replace(" ", "", $user->name) ]);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->unique('compact_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('compact_name');
        });
    }
}
