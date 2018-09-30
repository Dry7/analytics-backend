<?php

namespace App\Providers;

use App\Models\Group;
use App\Observers\GroupObserver;
use App\Services\CountryService;
use App\Services\DatabaseService;
use App\Services\ElasticSearchService;
use App\Services\InfluxService;
use Elasticsearch\ClientBuilder;
use Illuminate\Support\ServiceProvider;
use InfluxDB\Client;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Group::observe(GroupObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(CountryService::class);
        $this->app->singleton(Client::class, function () {
            return new Client(
                config('services.influx.host'),
                config('services.influx.port'),
                config('services.influx.username'),
                config('services.influx.password')
            );
        });
        $this->app->singleton(InfluxService::class, function () {
            $service = app(Client::class);
            return new InfluxService(
                $service,
                $service->selectDB(config('services.influx.database'))
            );
        });
        $this->app->singleton(\Elasticsearch\Client::class, function () {
            return ClientBuilder::create()
                ->setHosts([config('services.elasticsearch.host') . ':' . config('services.elasticsearch.port')])
                ->build();
        });
        $this->app->singleton(ElasticSearchService::class);
        $this->app->singleton(DatabaseService::class);
    }
}
