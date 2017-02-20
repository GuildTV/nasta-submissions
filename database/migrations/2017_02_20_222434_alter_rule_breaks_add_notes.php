<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterRuleBreaksAddNotes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('uploaded_file_rule_breaks', function (Blueprint $table) {
            $table->text('notes')->after('result');
        });
        Schema::table('entry_rule_breaks', function (Blueprint $table) {
            $table->text('notes')->after('result');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('uploaded_file_rule_breaks', function (Blueprint $table) {
            $table->dropColumn('notes');
        });
        Schema::table('entry_rule_breaks', function (Blueprint $table) {
            $table->dropColumn('notes');
        });
    }
}
