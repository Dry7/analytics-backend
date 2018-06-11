<?php

namespace App\Services;

use App\Models\Group;
use App\Models\Link;
use App\Models\Post;
use App\Types\Network;
use Carbon\Carbon;
use GuzzleHttp\Client;

class VKService
{
    /** @var Client */
    private $client;

    /** @var InfluxService */
    private $influx;

    /**
     * VKService constructor.
     * @param Client $client
     * @param InfluxService $influx
     */
    public function __construct(Client $client, InfluxService $influx)
    {
        $this->client = $client;
        $this->influx = $influx;
    }

    /**
     * @param array $data
     *
     * @throws \InfluxDB\Database\Exception
     * @throws \InfluxDB\Exception
     */
    public function saveAll(array $data)
    {
        $group = $this->save($data);

        if ((int)$group->members < 5000) {
            return;
        }

        foreach ($data['wall'] as $post) {
            $this->savePost($group, (array)$post);
        }

        $calculateIncrements = $this->saveHistory($group);

        $group->fill($calculateIncrements);
        $group->save();
    }

    /**
     * @param Group $group
     *
     * @throws \InfluxDB\Database\Exception
     * @throws \InfluxDB\Exception
     *
     * @return array
     */
    public function saveHistory(Group $group): array
    {
        $data = [
            'members' => $group->members,
            'members_possible' => $group->members_possible,
            'posts' => $group->posts,
            'likes' => $group->getTotalLikes(),
            'shares' => $group->getTotalShares(),
            'comments' => $group->getTotalComments(),
            'posts_links' => $group->getTotalLinks(),
            'avg_posts_links' => $group->getAveragePostsLinks(),
            'ads' => $group->getTotalAds(),
            'ads_avg_likes' => $group->getAverageAdsLikes(),
            'ads_avg_shares' => $group->getAverageAdsShares(),
            'ads_avg_views' => $group->getAverageAdsViews(),
            'ads_avg_comments' => $group->getAverageAdsComments(),
            'ads_avg_links' => $group->getAverageAdsLinks(),
            'avg_posts' => $group->getAveragePostsPerDay(),
        ] + $group->getCountsPerPost();

        $data += $this->calculateIncrements($group, $data);

        $this->influx->saveGroupHistory(
            ['group_id' => $group->id],
            $data
        );

        return collect($data)
            ->only([
                'members_day_inc', 'members_day_inc_percent', 'members_week_inc',
                'members_week_inc_percent', 'members_month_inc', 'members_month_inc_percent',
                'ads'
            ])
            ->toArray();
    }

    /**
     * @param Group $group
     * @param array $today
     * @return array
     */
    public function calculateIncrements(Group $group, array $today): array
    {
        echo "\n\$this->influx->getGroupByNameDate({$group->id}, " . now()->subDay() . ")";
        $yesterday = $this->influx->getGroupByNameDate($group->id, now()->subDay());
        $week      = $this->influx->getGroupByNameDate($group->id, now()->subWeek());
        $month     = $this->influx->getGroupByNameDate($group->id, now()->subMonth());

        $increments = [];

        list($increments['members_day_inc'],   $increments['members_day_inc_percent'])   = $this->getDifference($yesterday, $today);
        list($increments['members_week_inc'],  $increments['members_week_inc_percent'])  = $this->getDifference($week, $today);
        list($increments['members_month_inc'], $increments['members_month_inc_percent']) = $this->getDifference($month, $today);

        return $increments + $this->calculateDayIncrement($yesterday, $today);
    }

    /**
     * @param array|null $yesterday
     * @param array|null $today
     * @return array
     */
    public function calculateDayIncrement(?array $yesterday, ?array $today): array
    {
        return collect(['posts', 'likes', 'shares', 'comments'])->reduce(function ($array, $column) use ($yesterday, $today) {
            if (!isset($today[$column]) || !isset($yesterday[$column])) {
                $array[$column] = null;
            } else {
                $array[$column] = $today[$column] - $yesterday[$column];
            }
            return $array;
        }, collect([]))->mapWithKeys(function ($value, $key) {
            return [$key . '_per_day' => $value];
        })->toArray();
    }

    /**
     * @param array|null $from
     * @param array|null $to
     * @param string $column
     * @return array
     */
    public function getDifference(?array $from, ?array $to, string $column = 'members'): array
    {
        if (!isset($from[$column]) || !isset($to[$column])) {
            return [null, null];
        }

        $difference = $to[$column] - $from[$column];

        return [
            $difference,
            $difference !== 0 ? round($difference / ($from[$column] / 100), 2) : null,
        ];
    }

    /**
     * @param $data
     *
     * @return Group
     */
    public function save($data)
    {
        $group = Group::updateOrCreate(
            ['network_id' => Network::VKONTAKTE, 'source_id' => $data['source_id']],
            collect($data)->except(['url', 'links', 'photos', 'boards', 'audio', 'video', 'market'])->toArray()
        );

        return $group;
    }

    /**
     * @param Group $group
     * @param array $post
     */
    public function savePost(&$group, $post)
    {
        $model = Post::updateOrCreate(
            ['group_id' => $group->id, 'post_id' => $post['id']],
            collect($post)->only(['date', 'likes', 'shares', 'views', 'comments', 'is_pinned', 'is_ad'])->toArray()
            + ['links' => count($post['links'])]
        );

        $this->saveLinks($model, (array)$post['links']);
    }

    /**
     * @param Post $post
     * @param array $urls
     */
    public function saveLinks(Post &$post, array $urls)
    {
        foreach ($urls as $url) {
            Link::updateOrCreate(
                ['group_id' => $post->group_id, 'post_id' => $post->post_id, 'url' => $url]
            );
        }
    }

    /**
     * @param int $sourceId
     */
    public function touch(int $sourceId)
    {
        Group::whereSourceId($sourceId)->first()->touch();
    }
}
