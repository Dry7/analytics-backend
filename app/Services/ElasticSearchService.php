<?php

namespace App\Services;

use App\Models\Group;
use Elasticsearch\Client;
use Illuminate\Http\Request;

class ElasticSearchService
{
    private const INDEX = 'analytics';
    private const GROUPS = 'groups';
    private const GROUPS_RANGES = [
        'members', 'posts', 'likes', 'avg_posts', 'avg_comments_per_post', 'avg_likes_per_post',
        'avg_shares_per_post', 'avg_views_per_post',
    ];
    private const GROUPS_MATCHES = [
        'type_id', 'country', 'state', 'city', 'is_verified', 'is_closed', 'is_adult',
    ];
    private const DATE_FORMAT = 'yyyy-MM-dd HH:mm:ss';

    /** @var Client */
    protected $client;

    /**
     * ElasticSearchService constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function createIndex()
    {
        $this->client->indices()->delete([
            'index' => self::INDEX
        ]);
        $this->client->indices()->create([
            'index' => self::INDEX,
            'body' => [
                'mappings' => [
                    self::GROUPS => [
                        'properties' => [
                            'id'                        => [ 'type' => 'integer' ],
                            'network_id'                => [ 'type' => 'byte' ],
                            'type_id'                   => [ 'type' => 'byte' ],
                            'avatar'                    => [ 'type' => 'keyword' ],
                            'title'                     => [ 'type' => 'keyword' ],
                            'source_id'                 => [ 'type' => 'integer' ],
                            'slug'                      => [ 'type' => 'keyword' ],
                            'members'                   => [ 'type' => 'integer' ],
                            'members_possible'          => [ 'type' => 'integer' ],
                            'is_verified'               => [ 'type' => 'boolean' ],
                            'is_closed'                 => [ 'type' => 'boolean' ],
                            'is_adult'                  => [ 'type' => 'boolean' ],
                            'is_banned'                 => [ 'type' => 'boolean' ],
                            'in_search'                 => [ 'type' => 'boolean' ],
                            'posts'                     => [ 'type' => 'integer' ],
                            'posts_links'               => [ 'type' => 'integer' ],
                            'ads'                       => [ 'type' => 'integer' ],
                            'likes'                     => [ 'type' => 'integer' ],
                            'shares'                    => [ 'type' => 'integer' ],
                            'comments'                  => [ 'type' => 'integer' ],
                            'avg_posts'                 => [ 'type' => 'double' ],
                            'avg_comments_per_post'     => [ 'type' => 'double' ],
                            'avg_likes_per_post'        => [ 'type' => 'double' ],
                            'avg_shares_per_post'       => [ 'type' => 'double' ],
                            'avg_views_per_post'        => [ 'type' => 'double' ],
                            'members_day_inc'           => [ 'type' => 'integer' ],
                            'members_day_inc_percent'   => [ 'type' => 'double' ],
                            'members_week_inc'          => [ 'type' => 'integer' ],
                            'members_week_inc_percent'  => [ 'type' => 'double' ],
                            'members_month_inc'         => [ 'type' => 'integer' ],
                            'members_month_inc_percent' => [ 'type' => 'double' ],
                            'country_code'              => [ 'type' => 'keyword' ],
                            'state_code'                => [ 'type' => 'keyword' ],
                            'city_code'                 => [ 'type' => 'keyword' ],
                            'opened_at'                 => [ 'type' => 'date', 'format' => self::DATE_FORMAT ],
                            'last_post_at'              => [ 'type' => 'date', 'format' => self::DATE_FORMAT ],
                            'event_start'               => [ 'type' => 'date', 'format' => self::DATE_FORMAT ],
                            'event_end'                 => [ 'type' => 'date', 'format' => self::DATE_FORMAT ],
                            'cpp'                       => [ 'type' => 'integer' ],
                            'updated_at'                => [ 'type' => 'date', 'format' => self::DATE_FORMAT ],
                            'created_at'                => [ 'type' => 'date', 'format' => self::DATE_FORMAT ],
                        ]
                    ]
                ]
            ]
        ]);
    }

    public function searchGroup(Request $request)
    {
        $query = [];

        foreach (self::GROUPS_RANGES as $key) {
            if ($request->has($key . '_from')) {
                data_set($query, 'range.' . $key . '.gte', $request->input($key . '_from'));
            }
            if ($request->has($key . '_to')) {
                data_set($query, 'range.' . $key . '.lte', $request->input($key . '_to'));
            }
        }

        foreach (self::GROUPS_MATCHES as $key) {
            if ($request->has($key)) {
                data_set($query, 'match.' . $key, $request->input($key));
            }
        }
print_r($query);
//        exit();
        $groups = $this->client->search([
            'index' => self::INDEX,
            'type' => self::GROUPS,
            'body' => [
                'query' => $query
            ]
        ]);
        echo '<pre>';
        print_r($groups);
    }

    /**
     * @param Group $model
     */
    public function index($model)
    {
        $this->client->index([
            'index' => $model->getElasticSearchIndex(),
            'type'  => $model->getElasticSearchType(),
            'id'    => $model->getElasticSearchId(),
            'body'  => $model->getElasticSearchBody(),
        ]);
    }

    /**
     * @param Group $model
     */
    public function delete($model)
    {
        $this->client->delete([
            'index' => $model->getElasticSearchIndex(),
            'type'  => $model->getElasticSearchType(),
            'id'    => $model->getElasticSearchId(),
        ]);
    }
}