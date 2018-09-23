<?php

namespace App\Models;

use App\Traits\Searchable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class Group
 * @package App\Models
 *
 * @param int $id
 * @param int $network_id
 * @param int $type_id
 * @param string $avatar
 * @param string $title
 * @param int $source_id
 * @param string $slug
 * @param int $members
 * @param int $members_possible
 * @param bool $is_verified
 * @param bool $is_closed
 * @param bool $is_adult
 * @param bool $is_banned
 * @param bool $in_search
 * @param int $posts
 * @param int $posts_links
 * @param int $ads
 * @param int $likes
 * @param int $shares
 * @param int $comments
 * @param int $avg_posts
 * @param int $avg_comments_per_post
 * @param int $avg_likes_per_post
 * @param int $avg_shares_per_post
 * @param int $avg_views_per_post
 * @param int $members_day_inc
 * @param int $members_day_inc_percent
 * @param int $members_week_inc
 * @param int $members_week_inc_percent
 * @param int $members_month_inc
 * @param int $members_month_inc_percent
 * @param string $country_code
 * @param string $state_code
 * @param string $city_code
 * @param Carbon $opened_at
 * @param Carbon $last_post_at
 * @param Carbon $event_start
 * @param Carbon $event_end
 * @param int $cpp
 */
class Group extends Model
{
    use Searchable;

    protected $fillable = ['network_id', 'type_id', 'avatar', 'title', 'source_id', 'slug', 'members',
        'members_possible', 'is_verified', 'is_closed', 'is_adult', 'is_banned', 'in_search', 'posts', 'posts_links',
        'ads', 'likes', 'shares', 'comments', 'avg_posts', 'avg_comments_per_post', 'avg_likes_per_post',
        'avg_shares_per_post', 'avg_views_per_post', 'members_day_inc', 'members_day_inc_percent', 'members_week_inc',
        'members_week_inc_percent', 'members_month_inc', 'members_month_inc_percent', 'country_code', 'state_code',
        'city_code', 'opened_at', 'last_post_at', 'event_start', 'event_end', 'cpp',
    ];

    protected $dates = [
        'opened_at', 'last_post_at', 'event_start', 'event_end',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'is_closed' => 'boolean',
        'is_adult' => 'boolean',
        'is_banned' => 'boolean',
        'in_search' => 'boolean',
    ];

    /** @var array */
    private $activity;

    /** @var array */
    private $adsActivity;

    public function calculateActivity()
    {
        if (is_null($this->activity)) {
            $this->activity = DB::selectOne(
                'SELECT AVG(count), SUM(likes) as likes, SUM(shares) as shares, SUM(views) as views, SUM(comments) as comments, SUM(links) as links, AVG(avg_links) as avg_links FROM '
                . '(SELECT COUNT(*) as count, SUM(likes) as likes, SUM(shares) as shares, SUM(views) as views, SUM(comments) as comments, SUM(links) as links, AVG(links) as avg_links FROM posts WHERE group_id = ? GROUP BY date::date) t', [$this->id]);
        }

        return $this->activity;
    }

    public function calculateAds()
    {
        if (is_null($this->adsActivity)) {
            $this->adsActivity = DB::selectOne(
                'SELECT COUNT(*) as total, avg(likes) as avg_likes, AVG(shares) as avg_shares, AVG(views) as avg_views, AVG(comments) as avg_comments, AVG(links) as avg_links FROM '
                . '(SELECT likes, shares, views, comments, links FROM posts WHERE group_id = ? and is_ad = true) t', [$this->id]);
        }

        return $this->adsActivity;
    }

    public function getAveragePostsPerDay(): int
    {
        return round($this->calculateActivity()->avg);
    }

    public function getAveragePostsLinks(): int
    {
        return round($this->calculateActivity()->avg_links);
    }

    public function getAverageAdsLikes(): int
    {
        return round($this->calculateAds()->avg_likes);
    }

    public function getAverageAdsShares(): int
    {
        return round($this->calculateAds()->avg_shares);
    }

    public function getAverageAdsViews(): int
    {
        return round($this->calculateAds()->avg_views);
    }

    public function getAverageAdsComments(): int
    {
        return round($this->calculateAds()->avg_comments);
    }

    public function getAverageAdsLinks(): int
    {
        return round($this->calculateAds()->avg_links);
    }

    public function getTotalLikes(): int
    {
        return round($this->calculateActivity()->likes);
    }

    public function getTotalShares(): int
    {
        return round($this->calculateActivity()->shares);
    }

    public function getTotalViews(): int
    {
        return round($this->calculateActivity()->views);
    }

    public function getTotalComments(): int
    {
        return round($this->calculateActivity()->comments);
    }

    public function getTotalLinks(): int
    {
        return round($this->calculateActivity()->links);
    }

    public function getTotalAds(): int
    {
        return round($this->calculateAds()->total);
    }

    public function getCountsPerPost(): array
    {
        $counts = DB::selectOne('SELECT AVG(likes) as likes, AVG(shares) as shares, AVG(comments) as comments, AVG(views) as views FROM posts WHERE group_id = ?', [$this->id]);

        return [
            'avg_likes_per_post' => round($counts->likes ?? 0),
            'avg_shares_per_post' => round($counts->shares ?? 0),
            'avg_comments_per_post' => round($counts->comments ?? 0),
            'avg_views_per_post' => round($counts->views ?? 0),
        ];
    }

    public function getUrlAttribute(): string
    {
        return 'https://vk.com/' . $this->slug;
    }

    public function getElasticSearchBody(): array
    {
        return $this->toArray();
    }

    public function links()
    {
        return $this->hasMany(Link::class);
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }
}
