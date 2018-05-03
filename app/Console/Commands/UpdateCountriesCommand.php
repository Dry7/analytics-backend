<?php

namespace App\Console\Commands;

use App\Services\CountryService;
use Illuminate\Console\Command;

class UpdateCountriesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'analytics:update-countries';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load countries from VK API';

    /**
     * Execute the console command.
     *
     * @param CountryService $service
     */
    public function handle(CountryService $service)
    {
        $service->updateAll();
    }
}
