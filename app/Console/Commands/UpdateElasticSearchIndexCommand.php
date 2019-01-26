<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\ElasticSearchService;
use App\Services\GroupService;
use Illuminate\Console\Command;

class UpdateElasticSearchIndexCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'analytics:update-elastic-search-index';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update ElasticSearch Index';

    /**
     * Execute the console command.
     *
     * @param ElasticSearchService $service
     * @param GroupService $groupService
     *
     * @return mixed
     */
    public function handle(ElasticSearchService $service, GroupService $groupService)
    {
        $service->createIndex();
        foreach ($groupService->cursor() as $group) {
            $this->info($group->id);
            $service->index($group);
        }
    }
}
