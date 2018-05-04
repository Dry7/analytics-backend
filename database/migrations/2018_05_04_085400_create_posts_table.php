<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('group_id')->comment = 'ID группы в аналитике';
            $table->unsignedBigInteger('post_id')->comment = 'ID поста в социальной сети';
            $table->dateTime('date')->comment = 'Дата поста';
            $table->unsignedInteger('likes')->nullable()->comment = 'Актуальное количество лайков';
            $table->unsignedInteger('shares')->nullable()->comment = 'Актуальное количество репостов';
            $table->unsignedInteger('views')->nullable()->comment = 'Актуальное количество просмотров';
            $table->unsignedInteger('comments')->nullable()->comments = 'Актуальное количество комментариев';
            $table->unsignedTinyInteger('links')->nullable()->comment = 'Количество внешних ссылок';
            $table->boolean('is_pinned')->comments = 'Запись закреплена';
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
        Schema::dropIfExists('posts');
    }
}
