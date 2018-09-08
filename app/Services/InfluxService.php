<?php

namespace App\Services;

use Carbon\Carbon;
use InfluxDB\Client;
use InfluxDB\Point;
use InfluxDB\Database;

class InfluxService
{
    private const TABLE_GROUPS = 'groups';

    /** @var Client */
    private $client;

    /** @var Database */
    private $database;

    /**
     * InfluxService constructor.
     *
     * @param Client $client
     * @param Database $database
     */
    public function __construct(Client $client, Database $database)
    {
        $this->client = $client;
        $this->database = $database;
    }

    /**
     * @param array $tags
     * @param array $fields
     *
     * @throws Database\Exception
     * @throws \InfluxDB\Exception
     *
     * @return bool
     */
    public function saveGroupHistory(array $tags, array $fields)
    {
        $point = new Point(self::TABLE_GROUPS, null, $tags, $this->null2int($fields));

        return $this->write($point);
    }

    /**
     * @param array $fields
     * @return array
     */
    public function null2int(array $fields): array
    {
        return collect($fields)->map(function ($value) {
            return $value ?? -1;
        })->toArray();
    }

    /**
     * @param array $fields
     * @return array
     */
    public function int2null(array $fields): array
    {
        return collect($fields)->map(function ($value) {
            return $value === -1 ? null : $value;
        })->toArray();
    }

    /**
     * @throws Database\Exception
     * @throws \InfluxDB\Exception
     */
    public function fill()
    {
        for ($i = 31; $i >= 0; $i--) {
            $date = $this->getTime(Carbon::now()->subDay($i));
            $point = new Point(self::TABLE_GROUPS, null, ['group_id' => 1004], [
                'members' => 1000000 - 1000 * $i,
                'members_possible' => null,
                'posts' => 10000 - 5 * $i,
                'likes' => 30000 - 25 * $i,
                'shares' => 5000 - 3 * $i,
                'comments' => 10000 - 4 * $i,
                'avg_posts' => rand(3, 5),
                'avg_likes_per_post' => rand(3, 5),
                'avg_shares_per_post' => rand(3, 5),
                'avg_comments_per_post' => rand(3, 5),
                'avg_views_per_post' => rand(3, 5),
            ], $date);
            $this->write($point);
        }
    }

    /**
     * @param Point $point
     * @return bool
     * @throws \InfluxDB\Exception
     */
    private function write(Point $point)
    {
        return $this->database->writePoints([$point]);
    }

    /**
     * @param int $groupId
     * @param Carbon $date
     * @return array
     */
    public function getGroupByNameDate(int $groupId, Carbon $date): ?array
    {
        $group = array_first($this->database->getQueryBuilder()
            ->select('*')
            ->from(self::TABLE_GROUPS)
            ->limit(1)
            ->where(["group_id = '{$groupId}'"])
            ->setTimeRange(
                $date->startOfDay()->timestamp,
                $date->endOfDay()->timestamp
            )
            ->getResultSet()
            ->getPoints());

        return $this->int2null((array)$group);
    }

    /**
     * @param Carbon $date
     * @return float|int
     */
    public function getTime(Carbon $date)
    {
        return $date->timestamp * 1000000000;
    }
}
