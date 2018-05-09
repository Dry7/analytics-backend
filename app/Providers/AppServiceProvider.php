<?php

namespace App\Providers;

use App\Services\CountryService;
use App\Services\InfluxService;
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
        //
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
    }
}
