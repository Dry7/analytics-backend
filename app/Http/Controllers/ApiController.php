<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\PostCommentsRequest;
use App\Http\Requests\PostExportHashRequest;
use App\Http\Resources\SuccessResponse;
use App\Services\VKService;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    /**
     * @param string $network
     * @param Request $request
     * @param VKService $service
     *
     * @throws \InfluxDB\Database\Exception
     * @throws \InfluxDB\Exception
     */
    public function register(string $network, Request $request, VKService $service)
    {
        $service->saveAll((array)json_decode($request->getContent()));
    }

    /**
     * @param string $network
     * @param Request $request
     * @param VKService $service
     */
    public function touch(string $network, Request $request, VKService $service)
    {
        $service->touch((int)$request->input('source_id'));
    }

    /**
     * @param string $network
     * @param PostExportHashRequest $request
     * @param VKService $service
     *
     * @return SuccessResponse
     */
    public function savePostExportHash(string $network, PostExportHashRequest $request, VKService $service)
    {
        $service->savePostExportHash($request->getGroupId(), $request->getPostId(), $request->getExportHash());

        return new SuccessResponse();
    }

    /**
     * @param string $network
     * @param PostCommentsRequest $request
     * @param VKService $service
     *
     * @return SuccessResponse
     */
    public function savePostComments(string $network, PostCommentsRequest $request, VKService $service)
    {
        $service->savePostComments($request->getGroupId(), $request->getPostId(), $request->getComments());

        return new SuccessResponse();
    }
}