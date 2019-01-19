<?php

declare(strict_types=1);

namespace App\Http\Controllers;

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

    public function savePostComments(string $network, Request $request, VKService $service)
    {
        $data = (array)json_decode($request->getContent());

        if ($data['comments']) {
            $service->savePostComments($data['groupId'], $data['postId'], $data['comments']);
        }
    }
}