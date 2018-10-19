<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\VKService;
use GuzzleHttp\RequestOptions;
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

    public function savePostExportHash(string $network, Request $request, VKService $service)
    {
        $data = (array)json_decode($request->getContent());

        if ($data['exportHash']) {
            $service->savePostExportHash($data['groupId'], $data['postId'], $data['exportHash']);
        }
    }
}