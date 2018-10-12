<?php

declare(strict_types=1);

namespace App\Services\Api;

use VK\Client\VKApiClient;

class VKService
{
    private const OFFSET = 100;

    /** @var VKApiClient */
    private $api;

    /** @var string */
    private $apiKey;

    public function __construct(VKApiClient $api, string $apiKey)
    {
        $this->api = new VKApiClient();
        $this->apiKey = $apiKey;
    }
}