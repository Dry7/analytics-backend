<?php

use App\Helpers\MigrationTrait;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLinksTable extends Migration
{
    use MigrationTrait;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->createSequence('links');
        $this->createPartition('links');
        $this->createDefaultPartition('links');

        Schema::table('links', function (Blueprint $table) {
            $table->unsignedBigInteger('post_id')->comment = 'ID поста в социальной сети';
            $table->string('url', 500)->comment = 'Ссылка';
            $table->boolean('is_ad')->default(false)->comment = 'Реклама';
            $table->timestamps();

            $table->index(['group_id', 'post_id'], 'idx__posts__group_id_post_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('links');
        $this->dropSequence('links');
    }
}
