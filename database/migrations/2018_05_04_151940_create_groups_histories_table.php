<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupsHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groups_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('group_id')->comment = 'ID группы в аналитике';
            $table->date('date')->comment = 'Дата парсинга';
            $table->unsignedInteger('members')->default(0)->comment = 'Количество участников';
            $table->unsignedInteger('members_possible')->nullable()->comment = 'Возможные участники';
            $table->unsignedInteger('posts')->nullable()->comment = 'Количество записей на стене';
            $table->unsignedInteger('likes')->nullable()->comment = 'Количество лайков у группы';
            $table->unsignedInteger('avg_posts')->nullable()->comment = 'Среднее количество постов в день';
            $table->unsignedInteger('avg_comments_per_post')->nullable()->comment = 'Среднее количество комментариев к записи';
            $table->unsignedInteger('avg_likes_per_post')->nullable()->comment = 'Среднее количество лайков к записи';
            $table->unsignedInteger('avg_shares_per_post')->nullable()->comment = 'Среднее количество репостов записи';
            $table->unsignedInteger('avg_views_per_post')->nullable()->comment = 'Среднее количество просмотров записи';
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('groups_histories');
    }
}
