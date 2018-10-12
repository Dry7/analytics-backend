<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Group;
use App\Services\ElasticSearchService;

class GroupObserver
{
    /** @var ElasticSearchService */
    private $elasticSearch;

    /**
     * GroupObserver constructor.
     * @param ElasticSearchService $elasticSearchService
     */
    public function __construct(ElasticSearchService $elasticSearchService)
    {
        $this->elasticSearch = $elasticSearchService;
    }

    /**
     * @param Group $group
     */
    public function saved(Group $group)
    {
        $this->elasticSearch->index($group);
    }

    /**
     * @param Group $group
     */
    public function deleting(Group $group)
    {
        $this->elasticSearch->delete($group);
    }
}