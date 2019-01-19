<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class GroupResource
 *
 * @package App\Http\Resources
 */
class GroupResource extends JsonResource
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
            'id' => $this->id,
            'networkId' => $this->network_id,
            'typeId' => $this->type_id,
            'avatar' => $this->avatar,
            'title' => $this->title,
            'slug' => $this->slug,
            'url' => $this->url,
            'members' => $this->members,
            'membersPossible' => $this->members_possible,
            'isVerified' => $this->is_verified,
            'isClosed' => $this->is_closed,
            'isAdult' => $this->is_adult,
            'isBanned' => $this->is_banned,
            'posts' => $this->posts,
            'postsLinks' => $this->posts_links,
            'ads' => $this->ads,
            'likes' => $this->likes,
            'shares' => $this->shares,
            'comments' => $this->comments,
            'avgPosts' => $this->avg_posts,
            'avgCommentsPerPost' => $this->avg_comments_per_post,
            'avgLikesPerPost' => $this->avg_likes_per_post,
            'avgSharesPerPost' => $this->avg_shares_per_post,
            'avgViewsPerPost' => $this->avg_views_per_post,
            'membersDayInc' => $this->members_day_inc,
            'membersDayIncPercent' => $this->members_day_inc,
            'membersWeekInc' => $this->members_week_inc,
            'membersWeekIncPercent' => $this->members_week_inc_percent,
            'membersMonthInc' => $this->members_month_inc,
            'membersMonthIncPercent' => $this->members_month_inc_percent,
            'countryCode' => $this->country_code,
            'stateCode' => $this->state_code,
            'cityCode' => $this->city_code,
            'openedAt' => $this->opened_at,
            'lastPostAt' => $this->last_post_at !== null ? $this->last_post_at->toDateTimeString() : null,
            'eventStart' => $this->event_start,
            'eventEnd' => $this->event_end,
            'cpp' => $this->cpp,
        ];
    }

    public function withResponse($request, $response)
    {
        $response->header('Access-Control-Allow-Origin', '*');
    }
}
