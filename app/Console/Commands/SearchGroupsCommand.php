<?php

namespace App\Console\Commands;

use App\Jobs\UpdateGroupJob;
use App\Types\Network;
use Illuminate\Console\Command;

class SearchGroupsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'analytics:search-groups';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Search new groups';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        for ($i = 100123; $i <= 101123; $i++) {
            UpdateGroupJob::dispatch(Network::VKONTAKTE, 'club' . $i)->onQueue('vk:search-groups');
        }
    }
}
