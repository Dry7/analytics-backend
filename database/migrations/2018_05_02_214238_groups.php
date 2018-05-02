<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Groups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedTinyInteger('network_id')->comment = 'ID социальной сети';
            $table->unsignedTinyInteger('type_id')->comment = 'Тип сообщества';
            $table->string('avatar')->nullable()->comment = 'URL аватара';
            $table->string('title')->nullable()->comment = 'Заголовок';
            $table->unsignedTinyInteger('source_id')->comment = 'ID сообщества в социальной сети';
            $table->string('slug')->comment = 'URL сообщества';
            $table->unsignedInteger('members')->default(0)->comment = 'Количество участников';
            $table->unsignedInteger('cpp')->nullable()->comment = 'Цена поста';
            $table->boolean('is_verified')->default(false)->comment = 'Подтвержден';
            $table->boolean('is_closed')->default(false)->comment = 'Закрытое сообщество';
            $table->boolean('is_adult')->default(false)->comment = '18+';
            $table->boolean('is_banned')->default(false)->comment = 'Забанено';
            $table->boolean('in_search')->nullable()->comment = 'Доступно в поиске';
            $table->unsignedInteger('posts')->nullable()->comment = 'Количество записей на стене';
            $table->dateTime('opened_at')->nullable()->comment = 'Дата открытия';
            $table->dateTime('last_post_at')->nullable()->comment = 'Последняя запись';
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
        Schema::dropIfExists('groups');
    }
}
