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
    protected $signature = 'analytics:search-groups
                                        {--from= : First VK group ID}
                                        {--to= : Last VK group ID}';

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
        $from = (int)$this->option('from');
        $to   = (int)$this->option('to');

        $this->info('From: ' . $from);
        $this->info('To: ' . $to);

        for ($i = $from; $i <= $to; $i++) {
            UpdateGroupJob::dispatch(Network::VKONTAKTE, 'club' . $i)->onQueue('vk:search-groups');
        }
    }
}
