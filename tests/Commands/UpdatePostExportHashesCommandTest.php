<?php

declare(strict_types=1);

namespace Tests\Commands;

use App\Console\Commands\UpdatePostExportHashesCommand;
use App\Services\PostService;
use Mockery\MockInterface;
use Tests\TestCase;

class UpdatePostExportHashesCommandTest extends TestCase
{
    /** @var PostService|MockInterface */
    private $postService;

    public function setUp()
    {
        parent::setUp();

        $this->postService = \Mockery::spy(PostService::class);
    }

    /**
     * @test
     */
    public function executeWithoutData()
    {
        app()->instance(PostService::class, $this->postService);

        $this->postService->shouldReceive('chunkPostWithExportHashes')->once();

        $this->artisan(UpdatePostExportHashesCommand::class)->execute();
    }
}