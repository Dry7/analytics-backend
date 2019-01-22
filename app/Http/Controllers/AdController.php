<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\AdRequest;
use App\Http\Resources\PostResource;
use App\Services\AdService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AdController extends Controller
{
    /** @var AdService */
    private $service;

    /**
     * AdController constructor.
     *
     * @param AdService $service
     */
    public function __construct(AdService $service)
    {
        $this->service = $service;
    }

    /**
     * @param AdRequest $request
     *
     * @return AnonymousResourceCollection
     */
    public function ads(AdRequest $request): AnonymousResourceCollection
    {
        return PostResource::collection($this->service->ads($request));
    }
}