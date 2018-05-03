<?php

namespace App\Services;

use App\Models\Country;
use App\Services\Api\VKService;

class CountryService
{
    /** @var VKService */
    private $service;

    public function __construct(VKService $service)
    {
        $this->service = $service;
    }

    public function updateAll()
    {
        foreach ($this->service->getCountries() as $countries) {
            foreach ($countries as $country) {
                Country::updateOrCreate(
                    ['id' => $country['id']],
                    ['title' => $country['title']]
                );
            }
        }
    }
}