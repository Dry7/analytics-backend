<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdatePostExportHash implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var int */
    private $networkId;

    /** @var int */
    private $groupId;

    /** @var int */
    private $postId;

    /** @var int */
    public $tries = 3;

    /** @var int  */
    public $timeout = 60;

    /**
     * Create a new job instance.
     *
     * @param int $networkId
     * @param int $groupId
     * @param int $postId
     *
     * @return void
     */
    public function __construct(int $networkId, int $groupId, int $postId)
    {
        $this->networkId = $networkId;
        $this->groupId = $groupId;
        $this->postId = $postId;
    }

    /**
     * Execute the job.
     *
     * @return void
     *
     * @throws \Exception
     */
    public function handle()
    {
    }
}
