<?php

declare(strict_types=1);

namespace Tests\Commands;

use App\Console\Commands\CreatePartitionsCommand;
use App\Services\DatabaseService;
use Mockery\MockInterface;
use Tests\TestCase;

class CreatePartitionsCommandTest extends TestCase
{
    /** @var DatabaseService|MockInterface */
    private $databaseService;

    protected function setUp()
    {
        parent::setUp();

        $this->databaseService = \Mockery::mock(DatabaseService::class);

        app()->instance(DatabaseService::class, $this->databaseService);
    }

    /**
     * @test
     */
    public function fromAndTo()
    {
        $this->databaseService->shouldReceive('createPartitions')->with(1, 200);

        $this->artisan(CreatePartitionsCommand::class, ['--from' => 1, '--to' => 200])->execute();
    }

    /**
     * @test
     */
    public function onlyFrom()
    {
        $this->databaseService->shouldReceive('partitionsAutoCreation');

        $this->artisan(CreatePartitionsCommand::class, ['--from' => 1])->execute();
    }

    /**
     * @test
     */
    public function onlyTo()
    {
        $this->databaseService->shouldReceive('partitionsAutoCreation');

        $this->artisan(CreatePartitionsCommand::class, ['--to' => 1])->execute();
    }

    /**
     * @test
     */
    public function withoutOptions()
    {
        $this->databaseService->shouldReceive('partitionsAutoCreation');

        $this->artisan(CreatePartitionsCommand::class)->execute();
    }
}