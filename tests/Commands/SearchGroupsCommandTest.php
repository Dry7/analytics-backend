<?php

declare(strict_types=1);

namespace Tests\Commands;

use App\Console\Commands\SearchGroupsCommand;
use App\Jobs\UpdateGroupJob;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class SearchGroupsCommandTest extends TestCase
{
    /**
     * @test
     */
    public function executeOnce()
    {
        Queue::fake();

        $this->artisan(SearchGroupsCommand::class, ['--from' => 1, '--to' => 1])->execute();

        Queue::assertPushed(UpdateGroupJob::class, 1);
    }

    /**
     * @test
     */
    public function executeHundred()
    {
        Queue::fake();

        $this->artisan(SearchGroupsCommand::class, ['--from' => 1, '--to' => 100])->execute();

        Queue::assertPushed(UpdateGroupJob::class, 100);
    }

    /**
     * @test
     */
    public function withoutOptions()
    {
        Queue::fake();

        $this->artisan(SearchGroupsCommand::class)->assertExitCode(0);

        Queue::assertNotPushed(UpdateGroupJob::class);
    }
}