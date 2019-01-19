<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\CountryCollection;
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
        return response()->json($this->service->getStates($countryCode));
    }

    public function cities(string $countryCode, string $stateCode)
    {
        return response()->json($this->service->getCities($countryCode, $stateCode));
    }
}