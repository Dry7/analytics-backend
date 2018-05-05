<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Group extends Model
{
    protected $fillable = ['network_id', 'type_id', 'avatar', 'title', 'source_id', 'slug', 'members', 'members_possible',
        'is_verified', 'is_closed', 'is_adult', 'is_banned', 'in_search', 'posts', 'country_code', 'state_code', 'city_code',
        'opened_at', 'last_post_at', 'event_start', 'event_end', 'cpp',
    ];

    protected $dates = [
        'opened_at', 'last_post_at', 'event_start', 'event_end',
    ];

    private $activity;

    public function calculateActivity()
    {
        if (is_null($this->activity)) {
            $this->activity = DB::selectOne('SELECT AVG(count), SUM(likes) as likes, SUM(comments) as comments, SUM(shares) as shares FROM '
                . '(SELECT COUNT(*) as count, SUM(likes) as likes, SUM(comments) as comments, SUM(shares) as shares FROM posts WHERE group_id = ? GROUP BY date::date) t', [$this->id]);
        }

        return $this->activity;
    }

    public function getAveragePostsPerDay()
    {
        return round($this->calculateActivity()->avg);
    }

    public function getTotalLikes()
    {
        return round($this->calculateActivity()->likes);
    }

    public function getTotalComments()
    {
        return round($this->calculateActivity()->comments);
    }

    public function getTotalShares()
    {
        return round($this->calculateActivity()->shares);
    }

    public function getCountsPerPost()
    {
        $counts = DB::selectOne('SELECT AVG(likes) as likes, AVG(shares) as shares, AVG(comments) as comments, AVG(views) as views FROM posts WHERE group_id = ?', [$this->id]);

        return [
            'avg_likes_per_post' => round($counts->likes ?? 0),
            'avg_shares_per_post' => round($counts->shares ?? 0),
            'avg_comments_per_post' => round($counts->comments ?? 0),
            'avg_views_per_post' => round($counts->views ?? 0),
        ];
    }
}
