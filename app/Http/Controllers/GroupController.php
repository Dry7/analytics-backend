<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\GroupResource;
use App\Http\Resources\GroupShortCollection;
use App\Http\Resources\LinkResource;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class GroupController extends Controller
{
    public function groupsShort(Request $request): GroupShortCollection
    {
        $query = Group::query()
            ->select(['id', 'title'])
            ->orderBy('title', 'asc');

        if ($request->has('title')) { $query = $query->where('title', 'ilike', '%' . $request->input('title') . '%'); }

        return new GroupShortCollection(
            $query
                ->offset($request->input('offset', 0))
                ->limit($request->input('limit', 100))
                ->get()
        );
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