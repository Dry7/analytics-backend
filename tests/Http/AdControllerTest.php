<?php

declare(strict_types=1);

namespace Tests\Http;

use App\Http\Controllers\AdController;
use App\Http\Requests\AdRequest;
use App\Http\Resources\PostResource;
use App\Models\Group;
use App\Services\AdService;
use Mockery\MockInterface;
use Tests\TestCase;

class AdControllerTest extends TestCase
{
    /** @var AdService|MockInterface */
    private $service;

    /** @var AdController */
    private $controller;

    protected function setUp()
    {
        parent::setUp();

        $this->service = \Mockery::mock(AdService::class);
        $this->controller = new AdController($this->service);
    }

    /**
     * @test
     */
    public function groupsShort()
    {
        // arrange
        $request = new AdRequest();
        $groups = collect(factory(Group::class, 10));
        $expected = PostResource::collection($groups);
        $this->service->shouldReceive('ads')->once()->with($request)->andReturn($groups);

        // act
        $response = $this->controller->ads($request);

        // assert
        $this->assertEquals($expected, $response);
    }
}
