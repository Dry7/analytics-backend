<?php

namespace App\Console\Commands;

use App\Models\Group;
use App\Services\ElasticSearchService;
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
     *
     * @return mixed
     */
    public function handle(ElasticSearchService $service)
    {
        $service->createIndex();
        foreach (Group::cursor() as $group) {
            $this->info($group->id);
//            $service->delete($group);
            $service->index($group);
        }
    }
}
