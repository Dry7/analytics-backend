<?php

namespace App\Services\Html;

use Carbon\Carbon;
use GuzzleHttp\Client;

class VKService
{
    private const BASE_URL = 'https://vk.com/';

    public const TYPE_GROUP = 'group';
    public const TYPE_PUBLIC = 'public';

    private const INFO = [
        'links' => 'Ссылки',
        'photos' => 'Фотографии',
        'boards' => 'Обсуждения',
        'audio' => 'Аудиозаписи',
        'video' => 'Видео',
        'market' => 'Товары',
    ];

    /** @var Client */
    private $client;

    /**
     * VKService constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $slug
     */
    public function run(string $slug)
    {
        $html = $this->load($slug);

        $data = $this->parseHTML($html);
    }

    /**
     * @param string $slug
     * @return string
     */
    private function load(string $slug): string
    {
        return $this->client->get(self::BASE_URL . $slug)->getBody();
    }

    /**
     * @param string $html
     * @return array
     */
    private function parseHTML(string $html): array
    {
        $html = preg_replace('#<span class="num_delim"> </span>#i', '', $html);

        $result = [
            'id'           => null,
            'title'        => null,
            'members'      => null,
            'url'          => null,
            'is_verified'  => null,
            'opened_at'    => null,
            'last_post_at' => null,
            'avatar'       => null,
            'posts'        => null,
        ];

        if (preg_match('#<title>(.*)</title>#i', $html, $title)) {
            $result['title'] = $title[1];
        }

        if (preg_match('#<em class="pm_counter">(.*)</em>#i', $html, $members)) {
            $em = strpos($members[1], '</em>');
            if ($em !== false) {
                $members[1] = substr($members[1], 0, $em);
            }
            $result['members'] = preg_replace('/[^0-9]*/i', '', $members[1]);
        }

        $result['is_verified'] = preg_match('#<b class="verified"></b>#i', $html);
        $result['is_closed'] = preg_match('#Закрытая группа#i', $html);
        $result['is_adult'] = preg_match('#Мне исполнилось 18 лет#i', $html);
        $result['type'] = preg_match('#mhi_back">Страница</span>#i', $html) ? self::TYPE_PUBLIC : self::TYPE_GROUP;

        if (preg_match('#<dt>Дата основания:</dt><dd>(.*)</dd>#i', $html, $opened_at)) {
            $result['opened_at'] = $this->date2carbon($opened_at[1]);
        }

        if (preg_match_all('#<a class="wi_date"(?: [^>]*)>(.*)</a>#i', $html, $last_post_at)) {
            $result['last_post_at'] = $this->date2carbon($last_post_at[1][0]);
            if (preg_match('#запись закреплена#i', $html)
                && isset($last_post_at[1][1])
                && $this->date2carbon($last_post_at[1][1]) > $result['last_post_at']
            ) {
                $result['last_post_at'] = $this->date2carbon($last_post_at[1][1]);
            }
        }

        if (preg_match('#<img src="(.*)" class="pp_img#i', $html, $avatar)) {
            $result['avatar'] = $avatar[1];
        }

        if (preg_match('#<span class="slim_header_label">(.*)</span>#i', $html, $posts)) {
            $result['posts'] = preg_replace('/[^0-9]*/i', '', $posts[1]);
        } elseif (preg_match('#<a name="wall"></a>\s*<h4 class="slim_header">(.*)</h4>#i', $html, $posts)) {
            $result['posts'] = preg_replace('/[^0-9]*/i', '', $posts[1]);
        }

        if (preg_match('#<a href="\/wall\?act=toggle_subscribe\&owner_id=\-(\d*)&#i', $html, $id)) {
            $result['id'] = $id[1];
        }

        if (preg_match('#<link rel="canonical" href="([^"]*)" />#i', $html, $url)) {
            $result['url'] = $url[1];
        }

        foreach (self::INFO as $key => $value) {
            if (preg_match('#' . $value . ' <em class="pm_counter">([^<]*)</em>#i', $html, $item)) {
                $result[$key] = $item[1];
            } else {
                $result[$key] = null;
            }
        }

        return $result;
    }

    /**
     * @param string $date
     * @return Carbon
     */
    private function date2carbon(string $date)
    {
        switch ($date) {
            case 'час назад':
                return now()->subHour();

            case 'два часа назад':
                return now()->subHours(2);

            case 'три часа назад':
                return now()->subHours(3);

            case 'четыре часа назад':
                return now()->subHours(4);

            case 'пять часов назад':
                return now()->subHours(5);
        }

        if (preg_match('#\d (минут|минуты) назад#i', $date)) {
            return now()->subMinutes((int)$date);
        }

        if (preg_match('#\d (секунду|секунды) назад#i', $date)) {
            return now()->subSeconds((int)$date);
        }

        $months = [
            'янв' => 1,
            'января' => 1,
            'фев' => 2,
            'февраля' => 2,
            'мар' => 3,
            'марта' => 3,
            'апр' => 4,
            'апреля' => 4,
            'мая' => 5,
            'июн' => 6,
            'июня' => 6,
            'июл' => 7,
            'июля' => 7,
            'авг' => 8,
            'августа' => 8,
            'сен' => 9,
            'сентября' => 9,
            'окт' => 10,
            'октября' => 10,
            'ноя' => 11,
            'ноября' => 11,
            'дек' => 12,
            'декабря' => 12,
        ];

        $date = preg_replace('#сегодня в#i', now()->format('d.m.Y'), $date);
        $date = preg_replace('#вчера в#i', now()->subDay()->format('d.m.Y'), $date);

        $year = $this->getYear($date);

        foreach ($months as $val => $id) {
            $date = preg_replace('# ' . $val . ' #i', '.' . $id . '.' . $year, $date);
        }
        if (!preg_match('#\d{1,2}:\d{1,2}#i', $date)) {
            $date .= ' 00:00';
        }

        $date = preg_replace('#в#', '', $date);

        return Carbon::createFromFormat('d.m.Y H:i', $date);
    }

    /**
     * @param string $date
     * @return int|string
     */
    private function getYear(string $date)
    {
        for ($year = now()->year, $i = 0; $i < 20; $i++) {
            if (preg_match('#' . ($year - $i) . '#i', $date)) {
                return '';
            }
        }

        return now()->year;
    }

    public function loadAjaxWall()
    {
        $response = $this->client->request('POST', self::BASE_URL . 'al_wall.php', [
            'form_params' => [
                'act' => 'get_wall',
                'al' => 1,
                'fixed' => 41632,
                'offset' => 9,
                'onlyCache' => false,
                'owner_id' => -48210134,
                'type' => 'own',
                'wall_start_from' => 10,
            ]
        ]);
        $html = $response->getBody();
        $skip = strpos($html, '<div id="post-');
        if ($skip !== false) {
            $html = substr($html, $skip, strlen($html));
        }
        $skip = strrpos($html, '</div>');
        if ($skip !== false) {
            $html = substr($html, 0, $skip+6);
        }

        return iconv('cp1251', 'utf-8', html_entity_decode($html));
    }

    public function loadWall(int $groupId, int $offset = 0)
    {
        $response = $this->client->request('GET', self::BASE_URL . 'wall-' . $groupId . '?offset=' . $offset, [
            'query' => [
                'own' => 1,
                'offset' => $offset
            ],
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36'
            ]
        ]);
        return $response->getBody();
    }

    private function wall(int $groupId, int $offset = 0): \Generator
    {
        $html = $this->loadWall($groupId, $offset);

        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML($html);
        $xpath = new \DOMXPath($dom);

        $posts = $xpath->query('//div[@class="wall_item"]');

        if ($posts->length == 0) {
            $posts = $xpath->query('//div[contains(@class, "_post post")]');
        }

        /** @var \DOMElement $posts */
        foreach ($posts as $post) {
            yield [
                'id' => $this->getPostId($xpath, $post),
                'date' => $this->getPostDate($xpath, $post),
                'likes' => $this->getCount($xpath, $post, 'like'),
                'shares' => $this->getCount($xpath, $post, 'share'),
                'views' => $this->getCount($xpath, $post, 'views'),
                'comments' => $this->getComments($xpath, $post)
            ];
        }
    }

    public function getPostId(\DOMXPath &$xpath, \DOMElement &$post): ?int
    {
        $id = $xpath->query('.//a[contains(@class, "post__anchor")]', $post);

        if (!isset($id[0])) {
            return (int)array_last(explode('_', $post->getAttribute('data-post-id')));
        }

        return (int)array_last(explode('_', $id[0]->getAttribute('name')));
    }

    public function getPostDate(\DOMXPath &$xpath, \DOMElement &$post)
    {
        $date = $xpath->query('.//a[@class="wi_date"]', $post);

        if (!isset($date[0])) {
            $date = $xpath->query('.//span[@class="rel_date"]', $post);
            if (!isset($date[0])) {
                $date = $xpath->query('.//span[@class="rel_date rel_date_needs_update"]', $post);
                if (!isset($date[0])) {
                    return null;
                }
            }
        }

        return $this->date2carbon($date[0]->textContent);
    }

    public function getCount(\DOMXPath &$xpath, \DOMElement &$post, string $element): int
    {
        $count = $xpath->query('.//b[@class="v_' . $element . '"]', $post);

        if (!isset($count[0])) {
            $count = $xpath->query('.//div[contains(@class, "feedback_' . $element . '")]', $post);
            if (!isset($count[0])) {
                return 0;
            }
        }

        $count = $count[0]->textContent;
        $count = preg_replace('#K#i', '000', $count);
        $count = preg_replace('#M#i', '000000', $count);

        return (int)$count;
    }

    public function getComments(\DOMXPath &$xpath, \DOMElement &$post): int
    {
        try {
            $comments = $xpath->query('.//a[@class="wr_header"]', $post);
            if (!isset($comments[0])) {
                return 0;
            }
            return (int)array_last(explode('/', $comments[0]->getAttribute('offs')));
        } catch (\Exception $exception) {
            return 0;
        }
    }

    public function decode(string $text): string
    {
//        return iconv('cp1251', 'utf-8', iconv('utf-8', 'cp1252', $text));
        return iconv('cp1251', 'utf-8', $text);
    }
}