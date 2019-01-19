<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\CityCollection;
use App\Http\Resources\CountryCollection;
use App\Http\Resources\StateCollection;
use App\Services\CountryService;

class CountryController extends Controller
{
    /** @var CountryService */
    private $service;

    public function __construct(CountryService $service)
    {
        $this->service = $service;
    }

    public function countries()
    {
        return new CountryCollection($this->service->getCountries());
    }

    public function states(string $countryCode)
    {
        return new StateCollection($this->service->getStates($countryCode));
    }

    public function cities(string $countryCode, string $stateCode)
    {
        return new CityCollection($this->service->getCities($countryCode, $stateCode));
    }
}