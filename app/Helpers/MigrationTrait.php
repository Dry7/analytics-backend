<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

trait MigrationTrait
{
    public function createSequence(string $table): bool
    {
        return DB::statement("CREATE SEQUENCE {$table}_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 2147483647 START 1 CACHE 1;");
    }

    public function dropSequence(string $table): bool
    {
        return DB::statement("DROP SEQUENCE IF EXISTS {$table}_id_seq;");
    }

    public function comment(string $table, string $column, string $comment): bool
    {
        return DB::statement("COMMENT ON COLUMN \"public\".\"{$table}\".\"{$column}\" IS '{$comment}';");
    }

    public function createPartition(string $table, string $key = 'group_id', bool $groupId = true)
    {
        $statement = DB::statement("CREATE TABLE \"public\".\"{$table}\" (
                     \"id\" bigint DEFAULT nextval('{$table}_id_seq') NOT NULL
                     " . ($groupId ? ",\"group_id\" integer NOT NULL" : '') . "
                   ) PARTITION BY RANGE({$key});");

        if ($groupId) {
            $this->comment($table, 'group_id', 'ID группы в аналитике');
        }

        return $statement;
    }

    public function createDefaultPartition(string $table)
    {
        return DB::statement("CREATE TABLE \"public\".\"{$table}_default\" PARTITION OF \"public\".\"{$table}\" DEFAULT;");
    }
}