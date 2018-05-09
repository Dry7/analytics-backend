<?php

namespace App\Services;

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
        $point = new Point(self::TABLE_GROUPS, null, $tags, $fields);

        return $this->write($point);
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
}