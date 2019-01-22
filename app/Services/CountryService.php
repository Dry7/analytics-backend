<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Collection;
use MenaraSolutions\Geographer\Earth;
use MenaraSolutions\Geographer\Services\TranslationAgency;
use MenaraSolutions\Geographer\State;

class CountryService
{
    /** @var Earth */
    private $service;

    /**
     * CountryService constructor.
     *
     * @param Earth $service
     */
    public function __construct(Earth $service)
    {
        $this->service = $service->setLocale(TranslationAgency::LANG_RUSSIAN);
    }

    /**
     * @param string $address
     *
     * @return null|string
     */
    public function getCountryCode(string $address): ?string
    {
        $address = preg_replace('#Беларусь#i', 'Белоруссия', $address);

        foreach ($this->service->getCountries() as $country) {
            if (preg_match('#' . $country->name . '#i', $address)) {
                return $country->isoCode;
            }
        }

        return null;
    }

    /**
     * @return Collection
     */
    public function getCountries(): Collection
    {
        return collect($this->service->getCountries())
            ->map(function ($item) { return ['isoCode' => $item->isoCode, 'name' => $item->name]; })
            ->sortBy('name')
            ->values();
    }

    /**
     * @param string $countryCode
     *
     * @return Collection
     */
    public function getStates(string $countryCode): Collection
    {
        return collect($this->service->findOneByCode($countryCode)->getStates())
            ->map(function ($item) { return ['isoCode' => $item->isoCode, 'name' => $item->name]; })
            ->sortBy('name')
            ->values();
    }

    /**
     * @param string $countryCode
     * @param string $stateCode
     *
     * @return Collection
     */
    public function getCities(string $countryCode, string $stateCode): Collection
    {
        return collect(State::build($stateCode)->setLocale(TranslationAgency::LANG_RUSSIAN)->getCities())
            ->map(function ($item) { return ['geonamesCode' => $item->geonamesCode, 'name' => $item->name]; })
            ->sortBy('name')
            ->values();
    }
}