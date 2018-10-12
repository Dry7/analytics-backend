<?php

declare(strict_types=1);

use App\Helpers\MigrationTrait;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroups extends Migration
{
    use MigrationTrait;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->createSequence('groups');
        $this->createPartition('groups', 'id', false);
        $this->createDefaultPartition('groups');

        Schema::table('groups', function (Blueprint $table) {
            $table->unsignedTinyInteger('network_id')->comment = 'ID социальной сети';
            $table->unsignedTinyInteger('type_id')->comment = 'Тип сообщества';
            $table->string('avatar')->nullable()->comment = 'URL аватара';
            $table->string('title')->nullable()->comment = 'Заголовок';
            $table->unsignedBigInteger('source_id')->comment = 'ID сообщества в социальной сети';
            $table->string('slug')->comment = 'URL сообщества';
            $table->unsignedInteger('members')->default(0)->comment = 'Количество участников';
            $table->unsignedInteger('members_possible')->nullable()->comment = 'Возможные участники';
            $table->boolean('is_verified')->default(false)->comment = 'Подтвержден';
            $table->boolean('is_closed')->default(false)->comment = 'Закрытое сообщество';
            $table->boolean('is_adult')->default(false)->comment = '18+';
            $table->boolean('is_banned')->default(false)->comment = 'Забанено';
            $table->boolean('in_search')->nullable()->comment = 'Доступно в поиске';
            $table->unsignedInteger('posts')->nullable()->comment = 'Количество записей на стене';
            $table->unsignedInteger('posts_links')->nullable()->comment = 'Количество внешних ссылок в сообщениях';
            $table->unsignedInteger('ads')->nullable()->comment = 'Количество рекламных записей на стене';
            $table->unsignedInteger('likes')->nullable()->comment = 'Количество лайков к сообщениям';
            $table->unsignedInteger('shares')->nullable()->comment = 'Количество репостов сообщений';
            $table->unsignedInteger('comments')->nullable()->comment = 'Количество комментариев к сообщениям';
            $table->unsignedInteger('avg_posts')->nullable()->comment = 'Среднее количество постов в день';
            $table->unsignedInteger('avg_comments_per_post')->nullable()->comment = 'Среднее количество комментариев к записи';
            $table->unsignedInteger('avg_likes_per_post')->nullable()->comment = 'Среднее количество лайков к записи';
            $table->unsignedInteger('avg_shares_per_post')->nullable()->comment = 'Среднее количество репостов записи';
            $table->unsignedInteger('avg_views_per_post')->nullable()->comment = 'Среднее количество просмотров записи';
            $table->integer('members_day_inc')->nullable()->comment = 'Прирост количества подписчиков за день';
            $table->decimal('members_day_inc_percent', 20, 2)->nullable()->comment = 'Прирост количества подписчиков за день в процентах';
            $table->integer('members_week_inc')->nullable()->comment = 'Прирост количества подписчиков за неделю';
            $table->decimal('members_week_inc_percent', 20, 2)->nullable()->comment = 'Прирост количества подписчиков за неделю в процентах';
            $table->integer('members_month_inc')->nullable()->comment = 'Прирост количества подписчиков за месяц';
            $table->decimal('members_month_inc_percent', 20, 2)->nullable()->comment = 'Прирост количества подписчиков за месяц в процентах';
            $table->string('country_code')->nullable()->comment = 'ISO код страны';
            $table->string('state_code')->nullable()->comment = 'ISO код региона';
            $table->string('city_code')->nullable()->comment = 'Geonames код города';
            $table->dateTime('opened_at')->nullable()->comment = 'Дата открытия';
            $table->dateTime('last_post_at')->nullable()->comment = 'Последняя запись';
            $table->dateTime('event_start')->nullable()->comment = 'Начало события';
            $table->dateTime('event_end')->nullable()->comment = 'Окончание события';
            $table->unsignedInteger('cpp')->nullable()->comment = 'Цена поста';
            $table->timestamps();

            $table->index('source_id', 'idx__groups__source_id');
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
        $this->dropSequence('groups');
    }
}
