<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LinksFk extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->unique(['group_id', 'post_id'], 'uq__posts__group_id_post_id');
        });
        Schema::table('links', function (Blueprint $table) {
            $table->foreign(['group_id', 'post_id'], 'fk__links__group_id_post_id')
                ->references(['group_id', 'post_id'])
                ->on('posts')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->dropForeign('fk__links__group_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('links', function (Blueprint $table) {
            $table->dropForeign('fk__links__group_id_post_id');
            $table->foreign('group_id', 'fk__links__group_id')
                ->references('id')
                ->on('groups')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
        Schema::table('posts', function (Blueprint $table) {
            $table->dropUnique('uq__posts__group_id_post_id');
        });
    }
}
