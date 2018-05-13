<?php

namespace App\Services;

use Elasticsearch\Client;

class ElasticSearchService
{
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

    public function index($model)
    {
        $this->client->index([
            'index' => $model->getElasticSearchIndex(),
            'type' => $model->getElasticSearchType(),
            'id' => $model->getElasticSearchId(),
            'body' => $model->getElasticSearchBody(),
        ]);
    }

    public function delete($model)
    {
        $this->client->delete([
            'index' => $model->getElasticSearchIndex(),
            'type' => $model->getElasticSearchType(),
            'id' => $model->getElasticSearchId(),
        ]);
    }
}