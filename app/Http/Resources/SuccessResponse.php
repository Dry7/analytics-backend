<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\JsonResponse;

class SuccessResponse extends JsonResponse
{
    public const RESPONSE = ['success' => true];

    public function  __construct()
    {
        parent::__construct(self::RESPONSE);
    }
}