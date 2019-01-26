<?php

declare(strict_types=1);

namespace Tests\Commands;

use App\Models\Group;
use App\Console\Commands\UpdateElasticSearchIndexCommand;
use App\Services\ElasticSearchService;
use App\Services\GroupService;
use Mockery\MockInterface;
use Tests\TestCase;

class UpdateElasticSearchIndexCommandTest extends TestCase
{
    /** @var ElasticSearchService|MockInterface */
    private $elasticSearchService;

    /** @var GroupService|MockInterface */
    private $groupService;

    public function setUp()
    {
        parent::setUp();

        $this->elasticSearchService = \Mockery::spy(ElasticSearchService::class);
        $this->groupService = \Mockery::spy(GroupService::class);

        app()->instance(ElasticSearchService::class, $this->elasticSearchService);
        app()->instance(GroupService::class, $this->groupService);
    }

    /**
     * @test
     */
    public function executeWithoutData()
    {
        $this->elasticSearchService->shouldReceive('createIndex');
        $this->groupService->shouldReceive('cursor')->andReturn($this->array2iterator([]));

        $this->artisan(UpdateElasticSearchIndexCommand::class)->execute();
    }

    /**
     * @test
     */
    public function executeWithData()
    {
        $groups = factory(Group::class, 5)->make();
        $this
            ->elasticSearchService
            ->shouldReceive('createIndex');
        $this
            ->groupService
            ->shouldReceive('cursor')
            ->andReturn(
                $this->array2iterator($groups)
            );

        foreach ($groups as $group) {
            $this->elasticSearchService->shouldReceive('index')->with($group);
        }

        $this->artisan(UpdateElasticSearchIndexCommand::class)->execute();
    }
}