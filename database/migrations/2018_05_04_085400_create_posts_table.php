<?php

declare(strict_types=1);

use App\Helpers\MigrationTrait;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsTable extends Migration
{
    use MigrationTrait;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->createSequence('posts');
        $this->createPartition('posts');
        $this->createDefaultPartition('posts');

        Schema::table('posts', function (Blueprint $table) {
            $table->unsignedBigInteger('post_id')->comment = 'ID поста в социальной сети';
            $table->dateTime('date')->comment = 'Дата поста';
            $table->unsignedInteger('likes')->nullable()->comment = 'Актуальное количество лайков';
            $table->unsignedInteger('shares')->nullable()->comment = 'Актуальное количество репостов';
            $table->unsignedInteger('views')->nullable()->comment = 'Актуальное количество просмотров';
            $table->unsignedInteger('comments')->nullable()->comments = 'Актуальное количество комментариев';
            $table->unsignedTinyInteger('links')->nullable()->comment = 'Количество внешних ссылок';
            $table->boolean('is_pinned')->default(false)->comments = 'Запись закреплена';
            $table->boolean('is_ad')->default(false)->comment = 'Реклама';
            $table->boolean('is_gif')->default(false)->comment = 'Пост содержит анимированый GIF';
            $table->boolean('is_video')->default(false)->comment = 'Пост содержит видео';
            $table->unsignedInteger('video_group_id')->nullable()->comment = 'ID группы видеозаписи';
            $table->unsignedInteger('video_id')->nullable()->comment = 'ID видеозаписи';
            $table->unsignedInteger('shared_group_id')->nullable()->comment = 'ID группы репоста';
            $table->unsignedInteger('shared_post_id')->nullable()->comment = 'ID репоста';
            $table->string('export_hash')->nullable()->comment = 'Ключ для экспорта поста';
            $table->timestamps();

            $table->unique(['group_id', 'post_id'], 'uq__posts__group_id_post_id');
            $table->index('group_id', 'idx__posts__group_id');
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
        $this->dropSequence('posts');
    }
}
