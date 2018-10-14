<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class GroupShort
 *
 * @package App\Http\Resources
 */
class GroupShortResource extends JsonResource
{
    public static $wrap = 'adsasd';

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
        ];
    }

    public function withResponse($request, $response)
    {
        $response->header('Access-Control-Allow-Origin', '*');
    }
}
