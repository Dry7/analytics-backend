<?php

declare(strict_types=1);

namespace App\Traits;

trait Searchable
{
    public function getElasticSearchIndex()
    {
        return 'analytics';
    }

    public function getElasticSearchType()
    {
        return $this->getTable();
    }

    public function getElasticSearchId()
    {
        return $this->id;
    }

    public function getElasticSearchBody()
    {
        return $this->toArray();
    }
}