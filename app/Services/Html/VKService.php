<?php

namespace App\Services\Html;

use App\Models\Group;
use App\Services\CountryService;
use App\Types\Network;
use App\Types\Type;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class VKService
{
    private const BASE_URL = 'https://vk.com/';

    private const INFO = [
        'links' => 'Ссылки',
        'photos' => 'Фотографии',
        'boards' => 'Обсуждения',
        'audio' => 'Аудиозаписи',
        'video' => 'Видео',
        'market' => 'Товары',
        'members_possible' => 'Возможные участники',
    ];

    /** @var Client */
    private $client;

    /** @var CountryService */
    private $countryService;

    /**
     * VKService constructor.
     * @param Client $client
     * @param CountryService $countryService
     */
    public function __construct(Client $client, CountryService $countryService)
    {
        $this->client = $client;
        $this->countryService = $countryService;
    }

    /**
     * @param string $slug
     *
     * @throws \Exception
     */
    public function run(string $slug)
    {
        if (!($html = $this->load($slug))) {
            exit();
        }

        $data = $this->parseHTML($html);

        if (is_null($data['source_id']) || is_null($data['members'])) {
            return;
        }

        $this->save($data);
    }

    /**
     * @param $data
     */
    public function save($data)
    {
        Group::updateOrCreate(
            ['network_id' => Network::VKONTAKTE, 'slug' => $data['slug']],
            collect($data)->except(['url', 'links', 'photos', 'boards', 'audio', 'video', 'market', 'posts'])->toArray()
        );
    }

    /**
     * @param $slug
     * @throws \Exception
     */
    public function test($slug)
    {
        if (!($html = $this->load($slug))) {
            exit();
        }

        $data = $this->parseHTML($html);
        print_r($data);
        echo $html;
    }

    /**
     * @param string $slug
     * @return string
     */
    private function load(string $slug): ?string
    {
        try {
            return $this->client->get(self::BASE_URL . $slug)->getBody();
        } catch (RequestException $exception) {
            echo $slug . ' - banned';
            return null;
        }
    }

    /**
     * @param string $html
     * @return array
     *
     * @throws \Exception
     */
    private function parseHTML(string $html): array
    {
        if (preg_match('#Вы попытались загрузить более одной однотипной страницы в секунду.#i', $html)) {
            throw new \Exception('VK query limit exceeded');
        }

        $html = preg_replace('#<span class="num_delim"> </span>#i', '', $html);

        $result = [
            'source_id'    => null,
            'title'        => null,
            'members'      => null,
            'url'          => null,
            'slug'         => null,
            'is_verified'  => null,
            'opened_at'    => null,
            'last_post_at' => null,
            'avatar'       => null,
            'posts'        => null,
            'country_code' => null,
            'state_code'   => null,
            'city_code'    => null,
            'event_start'  => null,
            'event_end'    => null,
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
        $result['is_banned'] = preg_match('#Сообщество заблокировано в связи с возможным нарушением правил сайта.#i', $html)
        || preg_match('#Данный материал заблокирован на территории Российской Федерации#i', $html);

        if (preg_match('#mhi_back">Мероприятие</span>#i', $html)) {
            $result['type_id'] = Type::EVENT;
        } elseif (preg_match('#mhi_back">Страница</span>#i', $html)) {
            $result['type_id'] = Type::PUBLIC;
        } else {
            $result['type_id'] = Type::GROUP;
        }

        if (preg_match('#<dt>Дата основания:</dt><dd>(.*)</dd>#i', $html, $opened_at)) {
            $result['opened_at'] = $this->date2carbon($opened_at[1]);
        }

        if (preg_match('#<dl class="pinfo_row"><dt>Место:</dt><dd><a(?: [^>]*)>([^>]*)</a>#i', $html, $city)) {
            foreach ($this->countryService->findCity(strip_tags($city[1])) as $key => $value) {
                $result[$key] = $value;
            }
        }

        if (preg_match('#<dt>Начало:</dt><dd>([^>]*)</dd>#i', $html, $event_start)) {
            $result['event_start'] = $this->date2carbon($event_start[1]);
        }

        if (preg_match('#<dt>Окончание:</dt><dd>([^>]*)</dd>#i', $html, $event_end)) {
            $result['event_end'] = $this->date2carbon($event_end[1]);
        }

        if (preg_match('#<img src="(.*)" class="pp_img#i', $html, $avatar)) {
            $result['avatar'] = $avatar[1];
            if ($result['avatar'] === '/images/community_100.png') {
                $result['avatar'] = self::BASE_URL . substr($result['avatar'], 1, strlen($result['avatar']));
            }
        }

        if (preg_match('#<span class="slim_header_label">(.*)</span>#i', $html, $posts)) {
            $result['posts'] = preg_replace('/[^0-9]*/i', '', $posts[1]);
        } elseif (preg_match('#<a name="wall"></a>\s*<h4 class="slim_header">(.*)</h4>#i', $html, $posts)) {
            $result['posts'] = preg_replace('/[^0-9]*/i', '', $posts[1]);
        }

        if (!((int)$result['posts'] > 0)) {
            if (preg_match('#<input type="hidden" id="page_wall_count_own" value="(.*)" />#i', $html, $posts)) {
                $result['posts'] = (int)$posts[1];
            } else {
                $result['posts'] = null;
            }
        }

        if (preg_match('#<a href="\/wall\?act=toggle_subscribe\&owner_id=\-(\d*)&#i', $html, $source_id)) {
            $result['source_id'] = $source_id[1];
        }

        if (preg_match('#<link rel="canonical" href="([^"]*)" />#i', $html, $url)) {
            $result['url'] = $url[1];
            $result['slug'] = str_replace(self::BASE_URL, '', $result['url']);
        }

        foreach (self::INFO as $key => $value) {
            if (preg_match('#' . $value . ' <em class="pm_counter">([^<]*)</em>#i', $html, $item)) {
                $result[$key] = $item[1];
            } else {
                $result[$key] = null;
            }
        }

        foreach ($this->loadPostsFromGroup($html) as $key => $val) {
            $result[$key] = $val;
        }

        return $result;
    }

    public function loadPostsFromGroup(string $html)
    {
        $lastPostAt = null;

        if (!preg_match_all('#data-post-id="([^"]*)" data-post-click-type="post_owner_img"#i', $html, $ids)) {
            $ids = [1 => []];
        }

        if (!preg_match_all('#<a class="wi_date"(?: [^>]*)>([^<]*)</a>#i', $html, $dates)) {
            $dates = [1 => []];
        }

        if (!preg_match_all('#aria-label="(\d+) Нравится"><i class="i_like">#i', $html, $likes)) {
            $likes = [1 => []];
        }

        if (!preg_match_all('#aria-label="(\d+) Поделиться"><i class="i_share">#i', $html, $shares)) {
            $shares = [1 => []];
        }

        if (!preg_match_all('#no_views|aria-label="(\d+) (просмотр|просмотра|просмотров)*"><i class="i_views">#i', $html, $views)) {
            $views = [1 => []];
        }

        $posts = [];

        foreach ($ids[1] as $i => $id) {
            $date = $this->date2carbon($dates[1][$i]);
            if (is_null($lastPostAt) || $date > $lastPostAt) {
                $lastPostAt = $date;
            }
            $posts[] = [
                'id'     => array_last(explode('_', $id)),
                'dates'  => $date,
                'likes'  => $likes[1][$i],
                'shares' => $shares[1][$i],
                'views'  => $views[1][$i],
            ];
        }

        return [
            'last_post_at' => $lastPostAt,
            'posts'        => $posts,
        ];
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

        if (preg_match('#\d год#i', $date)) {
            return Carbon::createFromDate((int)$date, 1, 1);
        }

        foreach ([
            'Январь' => 1, 'Февраль' => 2, 'Март' => 3, 'Апрель' => 4, 'Май' => 5, 'Июнь' => 6, 'Июль' => 7,
                     'Август' => 8, 'Сентябрь' => 9, 'Октябрь' => 10, 'Ноябрь' => 11, 'Декабрь' => 12
                 ] as $value => $id) {
            if (preg_match('#' . $value . ' (\d*)#i', $date, $year)) {
                return Carbon::createFromDate($year[1], $id, 1);
            }
        }

        $months = [
            'января' => 1,
            'янв' => 1,
            'февраля' => 2,
            'фев' => 2,
            'марта' => 3,
            'мар' => 3,
            'апреля' => 4,
            'апр' => 4,
            'мая' => 5,
            'июня' => 6,
            'июн' => 6,
            'июля' => 7,
            'июл' => 7,
            'августа' => 8,
            'авг' => 8,
            'сентября' => 9,
            'сен' => 9,
            'октября' => 10,
            'окт' => 10,
            'ноября' => 11,
            'ноя' => 11,
            'декабря' => 12,
            'дек' => 12,
        ];

        $date = preg_replace('#сегодня в#i', now()->format('d.m.Y'), $date);
        $date = preg_replace('#вчера в#i', now()->subDay()->format('d.m.Y'), $date);
        $date = preg_replace('#Фераль#i', 'фев', $date);

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
        for ($year = now()->year, $i = 0; $i < 200; $i++) {
            if (preg_match('#' . ($year - $i) . '#i', $date)) {
                return '';
            }
        }
        for ($year = now()->year, $i = 0; $i < 200; $i++) {
            if (preg_match('#' . ($year + $i) . '#i', $date)) {
                return '';
            }
        }

        return now()->year;
    }

    /**
     * @param int $groupId
     * @param int $offset
     * @return mixed
     */
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

    /**
     * @param int $groupId
     * @param int $offset
     * @return \Generator
     */
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

    /**
     * @param \DOMXPath $xpath
     * @param \DOMElement $post
     * @return int|null
     */
    public function getPostId(\DOMXPath &$xpath, \DOMElement &$post): ?int
    {
        $id = $xpath->query('.//a[contains(@class, "post__anchor")]', $post);

        if (!isset($id[0])) {
            return (int)array_last(explode('_', $post->getAttribute('data-post-id')));
        }

        return (int)array_last(explode('_', $id[0]->getAttribute('name')));
    }

    /**
     * @param \DOMXPath $xpath
     * @param \DOMElement $post
     * @return Carbon|null
     */
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

    /**
     * @param \DOMXPath $xpath
     * @param \DOMElement $post
     * @param string $element
     * @return int
     */
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

    /**
     * @param \DOMXPath $xpath
     * @param \DOMElement $post
     * @return int
     */
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

    /**
     * @param string $text
     * @return string
     */
    public function decode(string $text): string
    {
//        return iconv('cp1251', 'utf-8', iconv('utf-8', 'cp1252', $text));
        return iconv('cp1251', 'utf-8', $text);
    }
}