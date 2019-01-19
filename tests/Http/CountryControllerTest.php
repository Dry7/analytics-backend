<?php

declare(strict_types=1);

namespace Tests\Services;

use App\Http\Middleware\ApiAuth;
use App\Http\Resources\SuccessResponse;
use App\Services\CountryService;
use App\Services\VKService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use MenaraSolutions\Geographer\Earth;
use Tests\TestCase;

class CountryControllerTest extends TestCase
{
    /** @var CountryService|\Mockery\MockInterface */
    private $service;

    protected function setUp()
    {
        parent::setUp();

        $this->service = \Mockery::mock(CountryService::class);

        app()->instance(CountryService::class, $this->service);
    }

    /**
     * @test
     */
    public function countries()
    {
        // arrange
        $json = [
            ['isoCode' => 'AU', 'name' => 'Австралия'],
            ['isoCode' => 'AT', 'name' => 'Австрия']
        ];
        $this->service->shouldReceive('getCountries')->once()->andReturn(collect($json));

        // act
        $response = $this->json('GET', '/api/countries');

        // assert
        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJson($json)
            ->assertJsonCount(2);
    }

    /**
     * @test
     */
    public function states()
    {
        // arrange
        $json = [
            ['isoCode' => 'BY-BR', 'name' => 'Брестская область'],
            ['isoCode' => 'BY-VI', 'name' => 'Витебская область'],
            ['isoCode' => 'BY-HO', 'name' => 'Гомельская область'],
            ['isoCode' => 'BY-HR', 'name' => 'Гродненская область'],
            ['isoCode' => 'BY-HM', 'name' => 'Минск'],
            ['isoCode' => 'BY-MI', 'name' => 'Минская область'],
            ['isoCode' => 'BY-MA', 'name' => 'Могилевская область'],
        ];
        $this->service->shouldReceive('getStates')->once()->andReturn(collect($json));

        // act
        $response = $this->json('GET', '/api/countries/BY/states');

        // assert
        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJson($json)
            ->assertJsonCount(7);
    }

    /**
     * @test
     */
    public function cities()
    {
        // arrange
        $json = [
            ['geonamesCode' => 467978, 'name' => 'Елец'],
            ['geonamesCode' => 535121, 'name' => 'Липецк'],
        ];
        $this->service->shouldReceive('getCities')->once()->andReturn(collect($json));

        // act
        $response = $this->json('GET', '/api/countries/RU/states/RU-LIP/cities');

        // assert
        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJson($json)
            ->assertJsonCount(2);
    }

    protected function withApiKey()
    {
        $apiKey = 'testKey';
        config()->set('scraper.api_key', $apiKey);

        return $this
            ->withHeader(ApiAuth::X_API_KEY, $apiKey);
    }
}
