<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class LinkResource
 *
 * @package App\Http\Resources
 */
class LinkResource extends JsonResource
{
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'groupId' => $this->resource->group_id,
            'url' => $this->resource->url,
            'isAd' => $this->resource->is_ad,
            'post' => new PostResource($this->resource->post),
        ];
    }

    public function withResponse($request, $response)
    {
        $response->header('Access-Control-Allow-Origin', '*');
    }
}
