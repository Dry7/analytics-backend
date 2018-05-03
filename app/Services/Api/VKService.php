<?php

namespace App\Services\Api;

use VK\Client\VKApiClient;

class VKService
{
    /** @var VKApiClient */
    private $api;

    public function __construct()
    {
        $this->api = new VKApiClient();

    }

    public function getCountries(): \Generator
    {
        $offset = 0;
        do {
            $countries = $this->api->database()->getCountries(config('vk.api.key'), [
                'need_all' => true,
                'offset' => $offset,
            ]);
            yield $countries['items'];
            $offset += 100;
        } while (count($countries['items']) == 100);
    }
}