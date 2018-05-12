<?php

namespace App\Jobs;

use App\Services\Html\VKService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateGroupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var int */
    private $networkId;

    /** @var string */
    private $url;

    /** @var int */
    public $tries = 3;

    /** @var int  */
    public $timeout = 60;

    /**
     * Create a new job instance.
     *
     * @param int $networkId
     * @param string $url
     *
     * @return void
     */
    public function __construct(int $networkId, string $url)
    {
        $this->networkId = $networkId;
        $this->url = $url;
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
