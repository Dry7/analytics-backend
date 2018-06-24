<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
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
            'postId' => $this->resource->post_id,
            'url' => $this->resource->url,
            'date' => $this->resource->date->toDateTimeString(),
            'likes' => $this->resource->likes,
            'shares' => $this->resource->shares,
            'views' => $this->resource->views,
            'comments' => $this->resource->comments,
        ];
    }
}
