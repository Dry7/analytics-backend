<?php

namespace App\Services\Html;

use App\Helpers\Utils;
use App\Models\Group;
use App\Models\Link;
use App\Models\Post;
use App\Services\CountryService;
use App\Services\InfluxService;
use App\Types\Network;
use App\Types\Type;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

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

    public function saveAll(array $data)
    {
        $group = $this->save($data);
        foreach ($data['wall'] as $post) {
            $this->savePost($group, (array)$post);
        }
    }

    /**
     * @param Group $group
     *
     * @throws \InfluxDB\Database\Exception
     * @throws \InfluxDB\Exception
     */
    public function runHistory(Group $group)
    {
        $data = [
            'members' => $group->members,
            'members_possible' => $group->members_possible,
            'posts' => $group->posts,
            'likes' => $group->getTotalLikes(),
            'avg_posts' => $group->getAveragePostsPerDay(),
        ] + $group->getCountsPerPost();

        $this->influx->saveGroupHistory(
            ['group_id' => $group->id],
            $data
        );
    }

    /**
     * @param $data
     *
     * @return Group
     */
    public function save($data)
    {
        $group = Group::updateOrCreate(
            ['network_id' => Network::VKONTAKTE, 'slug' => $data['slug']],
            collect($data)->except(['url', 'links', 'photos', 'boards', 'audio', 'video', 'market'])->toArray()
        );

        return $group;
    }

    public function savePost(&$group, $post)
    {
        $model = Post::updateOrCreate(
            ['group_id' => $group->id, 'post_id' => $post['id']],
            collect($post)->only(['date', 'likes', 'shares', 'views', 'comments', 'is_pinned', 'is_ad'])->toArray()
            + ['links' => count($post['links'])]
        );

        $this->saveLinks($model, $post['links']);
    }

    public function saveLinks(Post &$post, array $urls)
    {
        foreach ($urls as $url) {
            Link::updateOrCreate(
                ['group_id' => $post->group_id, 'post_id' => $post->post_id, 'url' => $url]
            );
        }
    }
}