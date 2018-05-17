<?php

namespace App\Http\Controllers;

use App\Services\Html\VKService;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function register(string $network, Request $request, VKService $service)
    {
        $service->saveAll((array)json_decode($request->getContent()));
    }
}