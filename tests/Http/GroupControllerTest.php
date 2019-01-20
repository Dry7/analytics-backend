<?php

declare(strict_types=1);

namespace Tests\Services;

use App\Http\Controllers\GroupController;
use App\Http\Requests\GroupsShortRequest;
use App\Http\Resources\GroupResource;
use App\Http\Resources\GroupShortCollection;
use App\Http\Resources\LinkResource;
use App\Models\Group;
use App\Models\Link;
use App\Models\Post;
use App\Services\GroupService;
use Mockery\MockInterface;
use Tests\TestCase;

class GroupControllerTest extends TestCase
{
    /** @var GroupService|MockInterface */
    private $service;

    /** @var GroupController */
    private $controller;

    protected function setUp()
    {
        parent::setUp();

        $this->service = \Mockery::mock(GroupService::class);
        $this->controller = new GroupController($this->service);
    }

    public static function groupsShortDataProvider()
    {
        return [
            [
                [
                    ['id' => 398, 'title' => '2Pac | Tupac Amaru Shakur'],
                    ['id' => 128, 'title' => '5 умных мыслей'],
                ],
                [
                    'title' => null,
                    'offset' => 0,
                    'limit' => 100,
                ],
            ],
            [
                [
                    ['id' => 400, 'title' => 'Comedy Club'],
                ],
                [
                    'title' => 'Comedy',
                    'offset' => 0,
                    'limit' => 10,
                ],
            ],
            [
                [
                    [],
                ],
                [
                    'title' => '132',
                    'offset' => 0,
                    'limit' => 100,
                ],
            ],
        ];
    }

    /**
     * @test
     *
     * @dataProvider groupsShortDataProvider
     *
     * @param array $groups
     * @param array $with
     */
    public function groupsShort(array $groups, array $with)
    {
        // arrange
        $request = new GroupsShortRequest($with);
        $groups = collect($groups);
        $expected = new GroupShortCollection($groups);
        $this->service->shouldReceive('getShortList')->once()->with(...array_values($with))->andReturn($groups);

        // act
        $response = $this->controller->groupsShort($request);

        // assert
        $this->assertEquals($expected, $response);
    }

    /**
     * @test
     */
    public function group()
    {
        // arrange
        $group = factory(Group::class)->make();
        $expected = new GroupResource($group);

        // act
        $response = $this->controller->group($group);

        // assert
        $this->assertEquals($expected, $response);
    }

    /**
     * @test
     */
    public function links()
    {
        // arrange
        $group = factory(Group::class)->make();
        $links = factory(Link::class, 10)->create();
        $expected = LinkResource::collection($links);
        $this->service->shouldReceive('links')->once()->with($group)->andReturn($links);

        // act
        $response = $this->controller->links($group);

        // assert
        $this->assertEquals($expected, $response);
    }
}
