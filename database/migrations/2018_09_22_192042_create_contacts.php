<?php

use App\Helpers\MigrationTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContacts extends Migration
{
    use MigrationTrait;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->createSequence('contacts');
        $this->createPartition('contacts');
        $this->createDefaultPartition('contacts');

        Schema::table('contacts', function (Blueprint $table) {
            $table->string('avatar')->nullable()->comment = 'URL фото';
            $table->string('url')->comment = 'URL в социальной сети';
            $table->string('name')->nullable()->comment = 'Имя';
            $table->boolean('active')->default(true)->comment = 'Активность';
            $table->timestamps();

            $table->index(['group_id'], 'idx__contacts__group_id');
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
        $this->dropSequence('contacts');
    }
}
