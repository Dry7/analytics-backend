<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\UpdatePostExportHash;
use App\Models\Post;
use App\Types\Network;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class UpdatePostExportHashesCommand extends Command
{
    private const CHUNK = 100;

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
     * @return mixed
     */
    public function handle(): void
    {
        $ids = $this->option('id');
        $all = (bool)$this->option('all');
        $dryRun = (bool)$this->option('dry-run');

        Post::query()
            ->when(empty($ids), function (Builder $query) use ($all) {
                return $query
                    ->when(!$all, function (Builder $query2) {
                        return $query2->where('is_ad', true);
                    })
                    ->whereNull('export_hash');
            })
            ->when(!empty($ids), function (Builder $query) use ($ids) {
                return $query->whereIn('id', $ids);
            })
            ->chunkById(self::CHUNK, function (Collection $posts) use ($dryRun) {
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
