<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use MenaraSolutions\Geographer\Earth;
use MenaraSolutions\Geographer\Services\TranslationAgency;

class CountryService
{
    private const CACHE_KEY_FIND_CITY = 'CountryService::findCity';

    /** @var Earth */
    private $service;

    public function __construct(Earth $service)
    {
        $this->service = $service->setLocale(TranslationAgency::LANG_RUSSIAN);
    }

    /**
     * @param string $address
     * @return null|string
     */
    public function getCountryCode(string $address): ?string
    {
        foreach ($this->service->getCountries() as $country) {
            if (preg_match('#' . $country->name . '#i', $address)) {
                return $country->isoCode;
            }
        }

        return null;
    }

    /**
     * @param string $address
     * @return array
     */
    public function findCity(string $address)
    {
        return Cache::rememberForever(self::CACHE_KEY_FIND_CITY . '::' . md5($address), function () use ($address) {
            return $this->parseAddress($address);
        });
    }

    private function parseAddress(string $address)
    {
        $countryCode = $this->getCountryCode($address);
        $stateCode = null;

        if (is_null($countryCode)) {
            return [
                'country_code' => $countryCode,
                'state_code' => $stateCode,
                'city_code' => null,
            ];
        }

        foreach($this->service->findOneByCode($countryCode)->getStates() as $state) {
            if (preg_match('#' . $state->name . '#i', $address)) {
                $stateCode = $state->isoCode;
            }
            foreach ($state->getCities() as $city) {
                if (preg_match('#' . $city->name . '#i', $address)) {
                    return [
                        'country_code' => $countryCode,
                        'state_code' => $state->isoCode,
                        'city_code' => $city->geonamesCode,
                    ];
                }
            }
        }

        return [
            'country_code' => $countryCode,
            'state_code' => $stateCode,
            'city_code' => null,
        ];
    }
}