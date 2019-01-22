<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\GroupRequest;
use App\Http\Requests\GroupsShortRequest;
use App\Http\Resources\GroupResource;
use App\Http\Resources\GroupShortCollection;
use App\Http\Resources\LinkResource;
use App\Models\Group;
use App\Services\GroupService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class GroupController extends Controller
{
    /** @var GroupService */
    private $service;

    /**
     * GroupController constructor.
     *
     * @param GroupService $service
     */
    public function __construct(GroupService $service)
    {
        $this->service = $service;
    }

    /**
     * @param GroupRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function groups(GroupRequest $request)
    {
        return response()->json($this->service->groups($request));
    }

    /**
     * @param GroupsShortRequest $request
     *
     * @return GroupShortCollection
     */
    public function groupsShort(GroupsShortRequest $request): GroupShortCollection
    {
        return new GroupShortCollection($this->service->getShortList($request->getTitle(), $request->getOffset(), $request->getLimit()));
    }

    /**
     * @param Group $group
     *
     * @return GroupResource
     */
    public function group(Group $group): GroupResource
    {
        return new GroupResource($group);
    }

    /**
     * @param Group $group
     *
     * @return AnonymousResourceCollection
     */
    public function links(Group $group): AnonymousResourceCollection
    {
        return LinkResource::collection($this->service->links($group));
    }

    /**
     * @param Group $group
     *
     * @return array
     */
    public function statistics(Group $group): array
    {
        return [];
    }
}