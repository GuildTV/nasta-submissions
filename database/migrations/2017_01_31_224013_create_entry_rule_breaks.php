<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntryRuleBreaks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entry_rule_breaks', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('entry_id')->unsigned()->unique();

            $table->enum('result', [ 'unknown', 'warning', 'break', 'ok', 'accepted', 'rejected', 'pending' ])->default('unknown');

            $table->text('constraint_map');

            $table->text('warnings');
            $table->text('errors');

            $table->timestamps();

            $table->foreign('entry_id')->references('id')->on('entries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entry_rule_breaks');
    }
}
