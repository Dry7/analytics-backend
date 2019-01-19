<?php

declare(strict_types=1);

namespace Tests\Services;

use App\Http\Middleware\ApiAuth;
use App\Http\Resources\SuccessResponse;
use App\Services\VKService;
use Illuminate\Http\Response;
use Tests\TestCase;

class ApiControllerTest extends TestCase
{
    /** @var VKService|\Mockery\MockInterface */
    private $service;

    protected function setUp()
    {
        parent::setUp();

        $this->service = \Mockery::mock(VKService::class);

        app()->instance(VKService::class, $this->service);
    }

    /**
     * @test
     */
    public function savePostExportHashSuccess()
    {
        // arrange
        $data = [
            'groupId' => 1,
            'postId' => 2,
            'exportHash' => 'KOXN44X3Fbzp9oJIRoEpOrVgkRMb',
        ];

        // act
        $response = $this
            ->withApiKey()
            ->json('POST', '/api/vk/posts/export-hash', $data);

        // expect
        $this
            ->service
            ->shouldHaveReceived('savePostExportHash')
            ->once()
            ->with(...array_values($data));

        // assert
        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(SuccessResponse::RESPONSE);
    }

    /**
     * @test
     */
    public function savePostExportHashEmptyRequest()
    {
        // act
        $response = $this
            ->withApiKey()
            ->json('POST', '/api/vk/posts/export-hash');

        // expect
        $this
            ->service
            ->shouldNotHaveReceived('savePostExportHash');

        // assert
        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(
                [
                    'message',
                    'errors' => [
                        'groupId',
                        'postId',
                        'exportHash',
                    ]
                ]
            );
    }

    /**
     * @test
     */
    public function savePostExportHashWithoutAuthKey()
    {
        // act
        $response = $this
            ->json('POST', '/api/vk/posts/export-hash');

        // expect
        $this
            ->service
            ->shouldNotHaveReceived('savePostExportHash');

        // assert
        $response
            ->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertSee('Invalid API key');
    }

    /**
     * @test
     */
    public function savePostCommentsSuccess()
    {
        // arrange
        $data = [
            'groupId' => 1,
            'postId' => 2,
            'comments' => 20,
        ];

        // act
        $response = $this
            ->withApiKey()
            ->json('POST', '/api/vk/posts/comments', $data);

        // expect
        $this
            ->service
            ->shouldHaveReceived('savePostComments')
            ->once()
            ->with(...array_values($data));

        // assert
        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(SuccessResponse::RESPONSE);
    }

    /**
     * @test
     */
    public function savePostCommentsEmptyRequest()
    {
        // act
        $response = $this
            ->withApiKey()
            ->json('POST', '/api/vk/posts/comments');

        // expect
        $this
            ->service
            ->shouldNotHaveReceived('savePostComments');

        // assert
        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(
                [
                    'message',
                    'errors' => [
                        'groupId',
                        'postId',
                        'comments',
                    ]
                ]
            );
    }

    /**
     * @test
     */
    public function savePostCommentsWithoutAuthKey()
    {
        // act
        $response = $this
            ->json('POST', '/api/vk/posts/comments');

        // expect
        $this
            ->service
            ->shouldNotHaveReceived('savePostComments');

        // assert
        $response
            ->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertSee('Invalid API key');
    }

    protected function withApiKey()
    {
        $apiKey = 'testKey';
        config()->set('scraper.api_key', $apiKey);

        return $this
            ->withHeader(ApiAuth::X_API_KEY, $apiKey);
    }
}
