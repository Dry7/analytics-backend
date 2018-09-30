<?php

namespace App\Console\Commands;

use App\Services\DatabaseService;
use Illuminate\Console\Command;

class CreatePartitionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'analytics:create-partitions
                                        {--from= : First ID}
                                        {--to= : Last ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new partitions';

    /**
     * Execute the console command.
     *
     * @param DatabaseService $service
     *
     * @return mixed
     */
    public function handle(DatabaseService $service): void
    {
        $from = (int)$this->option('from');
        $to   = (int)$this->option('to');

        if ($from > 0 && $to > 0) {
            $service->createPartitions($from, $to);
        } else {
            $service->partitionsAutoCreation();
        }
    }
}
