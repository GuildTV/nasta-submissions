<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterStationFoldersAddCategoryId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('station_folders', function (Blueprint $table) {
            $table->string('category_id')->after('account_id')->nullable()->default(null);

            $table->index('user_id');
            $table->dropUnique('station_folders_user_id_unique');
            $table->unique(['user_id', 'category_id']);
        
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('dropbox_account_id')->after('type')->nullable()->default(null);
        
            $table->foreign('dropbox_account_id')->references('id')->on('dropbox_accounts')->onDelete('cascade');
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
            $table->dropForeign('users_dropbox_account_id_foreign');

            $table->dropColumn('dropbox_account_id');
        });

        Schema::table('station_folders', function (Blueprint $table) {
            $table->dropForeign('station_folders_category_id_foreign');

            $table->dropUnique('station_folders_user_id_category_id_unique');
            $table->unique('user_id');
            $table->dropIndex('station_folders_user_id_index');

            $table->dropColumn('category_id');
        });
    }
}
