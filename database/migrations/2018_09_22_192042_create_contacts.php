<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContacts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('group_id')->comment = 'ID группы в аналитике';
            $table->string('avatar')->nullable()->comment = 'URL фото';
            $table->string('url')->comment = 'URL в социальной сети';
            $table->string('name')->nullable()->comment = 'Имя';
            $table->boolean('active')->default(true)->comment = 'Активность';
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
        Schema::dropIfExists('contacts');
    }
}
