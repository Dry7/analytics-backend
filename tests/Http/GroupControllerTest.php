<?php

declare(strict_types=1);

namespace Tests\Services;

use App\Http\Controllers\GroupController;
use App\Http\Resources\GroupResource;
use App\Models\Group;
use Tests\TestCase;

class GroupControllerTest extends TestCase
{
    /** @var GroupController */
    private $controller;

    protected function setUp()
    {
        parent::setUp();

        $this->controller = new GroupController();
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
}
