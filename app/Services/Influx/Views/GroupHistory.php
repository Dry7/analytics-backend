<?php

declare(strict_types=1);

namespace App\Services\Influx\Views;

class GroupHistory
{
    public static function normalize(array $data)
    {
        return [
            'members' => (int)$data['members'],
            'members_possible' => (int)$data['members_possible'],
            'posts' => (int)$data['posts'],
            'likes' => (int)$data['likes'],
            'shares' => (int)$data['shares'],
            'comments' => (int)$data['comments'],
            'posts_links' => (int)$data['posts_links'],
            'avg_posts_links' => (float)$data['avg_posts_links'],
            'ads' => (int)$data['ads'],
            'ads_avg_likes' => (float)$data['ads_avg_likes'],
            'ads_avg_shares' => (float)$data['ads_avg_shares'],
            'ads_avg_views' => (float)$data['ads_avg_views'],
            'ads_avg_comments' => (float)$data['ads_avg_comments'],
            'ads_avg_links' => (float)$data['ads_avg_links'],
            'avg_posts' => (float)$data['avg_posts'],
            'avg_likes_per_post' => (float)$data['avg_likes_per_post'],
            'avg_shares_per_post' => (float)$data['avg_shares_per_post'],
            'avg_comments_per_post' => (float)$data['avg_comments_per_post'],
            'avg_views_per_post' => (float)$data['avg_views_per_post'],
            'members_day_inc' => (int)$data['members_day_inc'],
            'members_day_inc_percent' => (float)$data['members_day_inc_percent'],
            'members_week_inc' => (int)$data['members_week_inc'],
            'members_week_inc_percent' => (float)$data['members_week_inc_percent'],
            'members_month_inc' => (int)$data['members_month_inc'],
            'members_month_inc_percent' => (float)$data['members_month_inc_percent'],
            'posts_per_day' => (int)$data['posts_per_day'],
            'likes_per_day' => (int)$data['likes_per_day'],
            'shares_per_day' => (int)$data['shares_per_day'],
            'comments_per_day' => (int)$data['comments_per_day'],
        ];
    }
}