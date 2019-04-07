<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Closure;

class PostService
{
    private const CHUNK = 100;

    public function chunkPostWithExportHashes(array $ids, bool $all, Closure $closure)
    {
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
            ->chunkById(self::CHUNK, $closure);
    }
}