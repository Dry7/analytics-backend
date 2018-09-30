<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class DatabaseService
{
    private const PARTITION_TABLES = [
        'groups',
        'posts',
        'links',
        'contacts',
    ];
    private const ROWS_IN_PARTITION = 10;
    private const MIN_FREE_SPACE_IN_PARTITION = 25;

    public function partitionsAutoCreation(): void
    {
        if ($this->isNeedNewPartition(array_first(self::PARTITION_TABLES))) {
            $this->createNextPartitions();
        }
    }

    public function createNextPartitions(): void
    {
        echo "\ncreateNextPartitions";
        $max = $this->getMaxPartition($this->getAllPartitions(array_first(self::PARTITION_TABLES)));

        $this->createPartitions($max + 1, $max + self::ROWS_IN_PARTITION);
    }

    public function isNeedNewPartition(string $table): bool
    {
        echo "\n" . '$this->getMaxPartition($this->getAllPartitions($table)) - ' . $this->getMaxPartition($this->getAllPartitions($table));
        echo "\n" . '$this->getMaxId($table) - ' . $this->getMaxId($table);
        echo "\n" . 'self::ROWS_IN_PARTITION - ' . self::ROWS_IN_PARTITION;
        echo "\n" . 'self::MIN_FREE_SPACE_IN_PARTITION - ' . self::MIN_FREE_SPACE_IN_PARTITION;
        echo "\n" . 'percents - ' . (($this->getMaxPartition($this->getAllPartitions($table)) - $this->getMaxId($table))
            / self::ROWS_IN_PARTITION / 100);
        exit();
        return ($this->getMaxPartition($this->getAllPartitions($table)) - $this->getMaxId($table))
            / self::ROWS_IN_PARTITION / 100 < self::MIN_FREE_SPACE_IN_PARTITION;
    }

    public function createPartitions(int $from, int $to): void
    {
        foreach (self::PARTITION_TABLES as $table) {
            $this->createPartition($table, $from, $to);
        }
    }

    private function createPartition(string $table, int $from, int $to): void
    {
        $partitionTable = $table . '_' . $from . '_' . $to;
        $toValue = $to + 1;

        DB::statement("CREATE TABLE {$partitionTable} PARTITION OF {$table} FOR VALUES FROM ({$from}) TO ({$toValue})");
    }

    private function getMaxId(string $table): int
    {
        return (int)DB::table($table)->max('id');
    }

    public function getMaxPartition(array $partitions): int
    {
        $max = 0;

        foreach ($partitions as $partition) {
            if (preg_match("/FOR VALUES FROM \('(\d+)'\) TO \('(\d+)'\)/i", $partition->partition_expression, $values)) {
                if (isset($values[2]) and ($values[2] > $max)) {
                    $max = $values[2] - 1;
                }
            }
        }

        return $max;
    }

    public function getAllPartitions(string $table): array
    {
        return DB::select(
            'with recursive inh as (
                     select i.inhrelid, null::text as parent
                     from pg_catalog.pg_inherits i
                       join pg_catalog.pg_class cl on i.inhparent = cl.oid
                       join pg_catalog.pg_namespace nsp on cl.relnamespace = nsp.oid
                     where nsp.nspname = \'public\' and cl.relname = ?

                     union all

                     select i.inhrelid, (i.inhparent::regclass)::text
                     from inh
                     join pg_catalog.pg_inherits i on (inh.inhrelid = i.inhparent)
                   )

                   select c.relname as partition_name,
                     n.nspname as partition_schema,
                     pg_get_expr(c.relpartbound, c.oid, true) as partition_expression,
                     pg_get_expr(p.partexprs, c.oid, true) as sub_partition,
                     parent,
                     case p.partstrat
                       when \'l\' then \'LIST\'
                       when \'r\' then \'RANGE\'
                     end as sub_partition_strategy
                   from inh
                   join pg_catalog.pg_class c on inh.inhrelid = c.oid
                   join pg_catalog.pg_namespace n on c.relnamespace = n.oid
                   left join pg_partitioned_table p on p.partrelid = c.oid
                   order by n.nspname, c.relname
        ', [$table]);
    }
}