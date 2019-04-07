<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\UpdatePostExportHash;
use App\Services\PostService;
use App\Types\Network;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class UpdatePostExportHashesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'analytics:update-post-export-hashes {--id=*} {--all} {--dry-run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update post export hash';

    /**
     * Execute the console command.
     *
     * @param PostService $service
     *
     * @return mixed
     */
    public function handle(PostService $service): void
    {
        $ids = $this->option('id');
        $all = (bool)$this->option('all');
        $dryRun = (bool)$this->option('dry-run');

        $service->chunkPostWithExportHashes($ids, $all, function (Collection $posts) use ($dryRun) {
            foreach ($posts as $post) {
                if ($dryRun) {
                    $this->info("Process post #" . $post->id);
                } else {
                    UpdatePostExportHash::dispatch(Network::VKONTAKTE, $post->group->source_id, $post->post_id)->onQueue(config('analytics.queue.vk'));
                }
            }
        });
    }
}
