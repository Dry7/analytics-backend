<?php

declare(strict_types=1);

namespace App\Http\Controllers;

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

    public function __construct(GroupService $service)
    {
        $this->service = $service;
    }

    public function groupsShort(GroupsShortRequest $request): GroupShortCollection
    {
        return new GroupShortCollection($this->service->getShortList($request->getTitle(), $request->getOffset(), $request->getLimit()));
    }

    public function group(Group $group): GroupResource
    {
        return new GroupResource($group);
    }

    public function links(Group $group): AnonymousResourceCollection
    {
        return LinkResource::collection($group->links()->with('post')->get()->sortByDesc('post.date'));
    }

    public function statistics(Group $group): array
    {
        return [];
    }
}