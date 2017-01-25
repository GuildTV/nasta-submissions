<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterDropboxAccountsAddDropboxId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dropbox_accounts', function (Blueprint $table) {
            $table->integer('dropbox_id')->after('total_space')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dropbox_accounts', function (Blueprint $table) {
            $table->dropColumn('dropbox_id');
        });
    }
}
