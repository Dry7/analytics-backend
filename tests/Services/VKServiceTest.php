<?php

namespace Tests\Services;

use App\Helpers\Utils;
use App\Jobs\UpdatePostComments;
use App\Models\Contact;
use App\Models\Group;
use App\Models\Post;
use App\Services\InfluxService;
use App\Services\VKService;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Mockery\MockInterface;
use Tests\TestCase;

class VKServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @var Client|MockInterface */
    private $http;

    /** @var InfluxService|MockInterface */
    private $influx;

    /** @var VKService */
    private $service;

    protected function setUp()
    {
        parent::setUp();

        /** @var Client $client */
        $this->http = \Mockery::mock(Client::class);
        /** @var InfluxService $influx */
        $this->influx = \Mockery::mock(InfluxService::class);

        $this->service = new VKService($this->http, $this->influx);
    }

    public static function calculateIncrementsDataProvider()
    {
        return [
            [
                [],
                []
            ],
        ];
    }

    /**
     * @test
     *
     * @dataProvider calculateIncrementsDataProvider
     *
     * @param array $today
     * @param array $expected
     */
    public function calculateIncrements(array $today, array $expected)
    {
        $this->markTestSkipped();

        Carbon::setTestNow('2018-03-20 12:00:00');

        // arrange
        $group = factory(Group::class)->create();
        $this->influx->shouldReceive('getGroupByNameDate')->with($group->id, now()->subDay())->andReturn([]);
        $this->influx->shouldReceive('getGroupByNameDate')->with($group->id, now()->subWeek())->andReturn([]);
        $this->influx->shouldReceive('getGroupByNameDate')->with($group->id, now()->subMonth())->andReturn([]);
        $today = [

        ];

        // act
        $result = $this->service->calculateIncrements($group, $today);

        $this->assertSame($expected, $result);
    }

    public static function calculateDayIncrementDataProvider()
    {
        return [
            [
                null,
                null,
                [
                    'posts_per_day'    => null,
                    'likes_per_day'    => null,
                    'shares_per_day'   => null,
                    'comments_per_day' => null,
                ],
            ],
            [
                ['posts'],
                ['likes'],
                [
                    'posts_per_day'    => null,
                    'likes_per_day'    => null,
                    'shares_per_day'   => null,
                    'comments_per_day' => null,
                ],
            ],
            [
                [
                    'posts'    => 1,
                    'likes'    => 42,
                    'shares'   => 34,
                    'comments' => 15,
                ],
                [
                    'posts'    => 50,
                    'likes'    => 53,
                    'shares'   => 44,
                    'comments' => 21,
                ],
                [
                    'posts_per_day'    => 49,
                    'likes_per_day'    => 11,
                    'shares_per_day'   => 10,
                    'comments_per_day' => 6,
                ],
            ],
        ];
    }

    /**
     * @test
     *
     * @dataProvider calculateDayIncrementDataProvider
     *
     * @param array|null $from
     * @param array|null $to
     * @param array $expected
     */
    public function calculateDayIncrement(?array $from, ?array $to, array $expected)
    {
        // act
        $result = $this->service->calculateDayIncrement($from, $to);

        // assert
        $this->assertSame($expected, $result);
    }

    public static function getDifferenceDataProvider()
    {
        return [
            [
                null,
                null,
                'members',
                [null, null],
            ],
            [
                ['members' => 1],
                ['members' => 2],
                'posts',
                [null, null],
            ],
            [
                ['members' => 100],
                ['members' => 110],
                'members',
                [10, 10.0],
            ],
            [
                ['members' => 7336],
                ['members' => 7301],
                'members',
                [-35, -0.48],
            ],
        ];
    }

    /**
     * @test
     *
     * @dataProvider getDifferenceDataProvider
     *
     * @param array|null $from
     * @param array|null $to
     * @param string $column
     * @param array $expected
     */
    public function getDifference(?array $from, ?array $to, string $column, array $expected)
    {
        // act
        $result = $this->service->getDifference($from, $to, $column);

        // assert
        $this->assertSame($expected, $result);
    }

    /**
     * @test
     */
    public function saveGroup()
    {
        // arrange
        $data = [
            'source_id'        => 39936,
            'title'            => 'Гномы, Эльфы и Люди',
            'members'          => 7336,
            'url'              => 'https://vk.com/club39936',
            'slug'             => 'club39936',
            'is_verified'      => false,
            'opened_at'        => null,
            'last_post_at'     => '2017-06-12 00:00:00',
            'avatar'           => 'https:\/\/pp.userapi.com\/c5881\/g39936\/d_56db4f31.jpg',
            'posts'            => 1030,
            'country_code'     => 'UA',
            'state_code'       => 'UA-30',
            'city_code'        => 703448,
            'event_start'      => null,
            'event_end'        => null,
            'is_closed'        => false,
            'is_adult'         => false,
            'is_private'       => false,
            'is_banned'        => false,
            'type_id'          => 1,
            'links'            => 8,
            'photos'           => 4075,
            'boards'           => 32,
            'audio'            => 522,
            'video'            => 124,
            'market'           => null,
            'members_possible' => null,
            'wall'             => []
        ];

        // act
        $this->service->save($data);

        // assert
        $this->assertDatabaseHas('groups', [
            'source_id'        => 39936,
            'title'            => 'Гномы, Эльфы и Люди',
            'members'          => 7336,
            'slug'             => 'club39936',
            'is_verified'      => false,
            'opened_at'        => null,
            'last_post_at'     => '2017-06-12 00:00:00',
            'avatar'           => 'https:\/\/pp.userapi.com\/c5881\/g39936\/d_56db4f31.jpg',
            'posts'            => 1030,
            'country_code'     => 'UA',
            'state_code'       => 'UA-30',
            'city_code'        => 703448,
            'event_start'      => null,
            'event_end'        => null,
            'is_closed'        => false,
            'is_adult'         => false,
            'is_banned'        => false,
            'type_id'          => 1,
            'members_possible' => null,
        ]);
    }

    /**
     * @test
     */
    public function saveEvent()
    {
        // arrange
        $data = [
            'source_id'        => 39960,
            'title'            => 'Dance Planet 11',
            'members'          => 439,
            'url'              => 'https://vk.com/event39960',
            'slug'             => 'event39960',
            'is_verified'      => false,
            'opened_at'        => null,
            'last_post_at'     => null,
            'avatar'           => 'https://pp.userapi.com/c39/g39960/b_eb24a82.jpg',
            'posts'            => 4,
            'country_code'     => 'RU',
            'state_code'       => 'RU-SPE',
            'city_code'        => 498817,
            'event_start'      => '2007-04-30 18:00:00',
            'event_end'        => '2007-05-01 06:00:00',
            'is_closed'        => false,
            'is_adult'         => false,
            'is_banned'        => false,
            'type_id'          => 3,
            'links'            => null,
            'photos'           => null,
            'boards'           => null,
            'audio'            => null,
            'video'            => null,
            'market'           => null,
            'members_possible' => null,
            'wall'             => []
        ];

        // act
        $this->service->save($data);

        // assert
        $this->assertDatabaseHas('groups', [
            'source_id'        => 39960,
            'title'            => 'Dance Planet 11',
            'members'          => 439,
            'slug'             => 'event39960',
            'is_verified'      => false,
            'opened_at'        => null,
            'last_post_at'     => null,
            'avatar'           => 'https://pp.userapi.com/c39/g39960/b_eb24a82.jpg',
            'posts'            => 4,
            'country_code'     => 'RU',
            'state_code'       => 'RU-SPE',
            'city_code'        => 498817,
            'event_start'      => '2007-04-30 18:00:00',
            'event_end'        => '2007-05-01 06:00:00',
            'is_closed'        => false,
            'is_adult'         => false,
            'is_banned'        => false,
            'type_id'          => 3,
            'members_possible' => null,
        ]);
    }

    /**
     * @test
     */
    public function savePublic()
    {
        // arrange
        $data = [
            'source_id'        => 10077,
            'title'            => 'savepicture',
            'members'          => 1411,
            'url'              => 'https://vk.com/public10077',
            'slug'             => 'public10077',
            'is_verified'      => false,
            'opened_at'        => null,
            'last_post_at'     => '2018-01-04 12:43:00',
            'avatar'           => 'https://pp.userapi.com/c840334/v840334206/3d5fa/fqNivDTYgRM.jpg',
            'posts'            => 1,
            'country_code'     => null,
            'state_code'       => null,
            'city_code'        => null,
            'event_start'      => null,
            'event_end'        => null,
            'is_closed'        => false,
            'is_adult'         => false,
            'is_private'       => false,
            'is_banned'        => false,
            'type_id'          => 2,
            'links'            => null,
            'photos'           => 1,
            'boards'           => null,
            'audio'            => null,
            'video'            => null,
            'market'           => null,
            'members_possible' => null,
            'wall'             => []
        ];

        // act
        $this->service->save($data);

        // assert
        $this->assertDatabaseHas('groups', [
            'source_id'        => 10077,
            'title'            => 'savepicture',
            'members'          => 1411,
            'slug'             => 'public10077',
            'is_verified'      => false,
            'opened_at'        => null,
            'last_post_at'     => '2018-01-04 12:43:00',
            'avatar'           => 'https://pp.userapi.com/c840334/v840334206/3d5fa/fqNivDTYgRM.jpg',
            'posts'            => 1,
            'country_code'     => null,
            'state_code'       => null,
            'city_code'        => null,
            'event_start'      => null,
            'event_end'        => null,
            'is_closed'        => false,
            'is_adult'         => false,
            'is_banned'        => false,
            'type_id'          => 2,
            'members_possible' => null,
        ]);
    }

    /**
     * @test
     */
    public function savePostNewPost()
    {
        // arrange
        $group = factory(Group::class)->create();
        $post = [
            'id'        => 1693,
            'date'      => '2018-05-11 10:43:00',
            'likes'     => 1,
            'shares'    => 1,
            'views'     => 207,
            'comments'  => 0,
            'has_next_comments' => false,
            'is_pinned' => false,
            'is_ad'     => false,
            'links'     => [],
        ];

        // act
        $this->service->savePost($group, $post);

        // assert
        $this->assertDatabaseHas('posts', [
            'group_id'  => $group->id,
            'post_id'   => 1693,
            'date'      => '2018-05-11 10:43:00',
            'likes'     => 1,
            'shares'    => 1,
            'views'     => 207,
            'comments'  => 0,
            'links'     => 0,
            'is_pinned' => false,
            'is_ad'     => false,
        ]);
    }

    /**
     * @test
     */
    public function savePostExistsPost()
    {
        // arrange
        $group = factory(Group::class)->create();
        factory(Post::class)->create(['group_id' => $group->id, 'post_id' => 1672, 'created_at' => '2018-02-03 11:01:01']);
        $post = [
            'id'        => 1672,
            'date'      => '2018-04-21 18:30:00',
            'likes'     => 0,
            'shares'    => 0,
            'views'     => 35,
            'comments'  => 0,
            'has_next_comments' => false,
            'is_pinned' => false,
            'is_ad'     => true,
            'links'     => []
        ];

        // act
        $this->service->savePost($group, $post);

        // assert
        $this->assertDatabaseHas('posts', [
            'group_id'   => $group->id,
            'post_id'    => 1672,
            'date'       => '2018-04-21 18:30:00',
            'likes'      => 0,
            'shares'     => 0,
            'views'      => 35,
            'comments'   => 0,
            'is_pinned'  => false,
            'is_ad'      => true,
            'links'      => 0,
            'created_at' => '2018-02-03 11:01:01',
        ]);
    }

    /**
     * @test
     */
    public function savePostWithLinks()
    {
        // arrange
        $group = factory(Group::class)->create();
        $post = [
            'id'        => 13810,
            'date'      => '2018-03-23 10:33:00',
            'likes'     => 10,
            'shares'    => 1,
            'views'     => 1200,
            'comments'  => 0,
            'has_next_comments' => false,
            'is_pinned' => true,
            'is_ad'     => false,
            'links'     => [
                'http://glastonberry.ru/events/legion/',
                'https://ponominalu.ru/event/legion',
            ],
        ];

        // act
        $this->service->savePost($group, $post);

        // assert
        $this->assertDatabaseHas('posts', [
            'group_id'  => $group->id,
            'post_id'   => 13810,
            'date'      => '2018-03-23 10:33:00',
            'likes'     => 10,
            'shares'    => 1,
            'views'     => 1200,
            'comments'  => 0,
            'is_pinned' => true,
            'is_ad'     => false,
            'links'     => 2,
        ]);
    }

    /**
     * @test
     */
    public function savePostWithNextComments()
    {
        // arrange
        Queue::fake();
        $group = factory(Group::class)->create();
        $post = [
            'id'        => 1693,
            'date'      => '2018-05-11 10:43:00',
            'likes'     => 1,
            'shares'    => 1,
            'views'     => 207,
            'comments'  => 0,
            'has_next_comments' => true,
            'is_pinned' => false,
            'is_ad'     => false,
            'links'     => [],
        ];

        // act
        $this->service->savePost($group, $post);

        // assert
        Queue::assertPushedOn('vk', UpdatePostComments::class);
        $this->assertDatabaseHas('posts', [
            'group_id'  => $group->id,
            'post_id'   => 1693,
            'date'      => '2018-05-11 10:43:00',
            'likes'     => 1,
            'shares'    => 1,
            'views'     => 207,
            'comments'  => 0,
            'links'     => 0,
            'is_pinned' => false,
            'is_ad'     => false,
        ]);
    }

    /**
     * @test
     */
    public function saveLinks()
    {
        // arrange
        $post = factory(Post::class)->create();
        $links = [
            'http://yandex.ru',
            'https://goo.gl/cqDSvC',
        ];

        // act
        $this->service->saveLinks($post, $links);

        // assert
        $this->assertDatabaseHas('links', ['group_id' => $post->group_id, 'post_id' => $post->post_id, 'url' => 'http://yandex.ru']);
        $this->assertDatabaseHas('links', ['group_id' => $post->group_id, 'post_id' => $post->post_id, 'url' => 'https://goo.gl/cqDSvC']);
    }

    /**
     * @test
     */
    public function saveLinksEmpty()
    {
        // arrange
        $post = factory(Post::class)->create();

        // act
        $this->service->saveLinks($post, []);

        // assert
        $this->assertDatabaseMissing('links', ['group_id' => $post->group_id, 'post_id' => $post->post_id]);
    }

    /**
     * @test
     */
    public function saveContacts()
    {
        // arrange
        $group = factory(Group::class)->create();

        // act
        $this->service->saveContacts($group->id, [
            (object)[
                'avatar' => 'https://pp.userapi.com/c629111/v629111007/7aa63/YzvIv1CD2Cg.jpg?ava=1',
                'name' => 'Андрей Резников',
                'url' => 'https://vk.com/andreireznikov',
            ],
            (object)[
                'avatar' => null,
                'name' => 'Анна Резникова',
                'url' => 'https://vk.com/id136722',
            ],
            (object)[
                'avatar' => 'https://pp.userapi.com/c841529/v841529667/58e79/NseF7iEhWsc.jpg?ava=1',
                'name' => null,
                'url' => 'https://vk.com/tatyanagubanova',
            ],
        ]);

        // assert
        $this->assertDatabaseHas('contacts', [
            'group_id' => $group->id,
            'avatar' => 'https://pp.userapi.com/c629111/v629111007/7aa63/YzvIv1CD2Cg.jpg?ava=1',
            'name' => 'Андрей Резников',
            'url' => 'https://vk.com/andreireznikov',
        ]);
        $this->assertDatabaseHas('contacts', [
            'group_id' => $group->id,
            'avatar' => null,
            'name' => 'Анна Резникова',
            'url' => 'https://vk.com/id136722',
        ]);
        $this->assertDatabaseHas('contacts', [
            'group_id' => $group->id,
            'avatar' => 'https://pp.userapi.com/c841529/v841529667/58e79/NseF7iEhWsc.jpg?ava=1',
            'name' => null,
            'url' => 'https://vk.com/tatyanagubanova',
        ]);
        $this->assertSame(3, $group->contacts()->count());
    }

    /**
     * @test
     */
    public function saveContactsAndInactivateOld()
    {
        // arrange
        $group = factory(Group::class)->create();
        factory(Contact::class)->create([
            'group_id' => $group->id,
            'avatar' => 'https://pp.userapi.com/c831309/v831309317/176117/B3Rq2TDACH8.jpg?ava=1',
            'name' => 'Алексей Оболевич',
            'url' => 'https://vk.com/muzred_record',
        ]);
        factory(Contact::class)->create([
            'group_id' => $group->id,
            'avatar' => 'https://pp.userapi.com/c629111/v629111007/7aa63/YzvIv1CD2Cg.jpg?ava=1',
            'name' => 'Андрей Резников',
            'url' => 'https://vk.com/andreireznikov',
        ]);

        // act
        $this->service->saveContacts($group->id, [
            (object)[
                'avatar' => 'https://pp.userapi.com/c629111/v629111007/7aa63/YzvIv1CD2Cg.jpg?ava=1',
                'name' => 'Андрей Резников',
                'url' => 'https://vk.com/andreireznikov',
            ],
            (object)[
                'avatar' => null,
                'name' => 'Анна Резникова',
                'url' => 'https://vk.com/id136722',
            ],
            (object)[
                'avatar' => 'https://pp.userapi.com/c841529/v841529667/58e79/NseF7iEhWsc.jpg?ava=1',
                'name' => null,
                'url' => 'https://vk.com/tatyanagubanova',
            ],
        ]);

        // assert
        $this->assertDatabaseHas('contacts', [
            'group_id' => $group->id,
            'avatar' => 'https://pp.userapi.com/c831309/v831309317/176117/B3Rq2TDACH8.jpg?ava=1',
            'name' => 'Алексей Оболевич',
            'url' => 'https://vk.com/muzred_record',
            'active' => false,
        ]);
        $this->assertDatabaseHas('contacts', [
            'group_id' => $group->id,
            'avatar' => 'https://pp.userapi.com/c629111/v629111007/7aa63/YzvIv1CD2Cg.jpg?ava=1',
            'name' => 'Андрей Резников',
            'url' => 'https://vk.com/andreireznikov',
            'active' => true,
        ]);
        $this->assertDatabaseHas('contacts', [
            'group_id' => $group->id,
            'avatar' => null,
            'name' => 'Анна Резникова',
            'url' => 'https://vk.com/id136722',
            'active' => true,
        ]);
        $this->assertDatabaseHas('contacts', [
            'group_id' => $group->id,
            'avatar' => 'https://pp.userapi.com/c841529/v841529667/58e79/NseF7iEhWsc.jpg?ava=1',
            'name' => null,
            'url' => 'https://vk.com/tatyanagubanova',
            'active' => true,
        ]);
        $this->assertSame(4, $group->contacts()->count());
    }

    /**
     * @test
     */
    public function saveContactsEmpty()
    {
        // arrange
        $group = factory(Group::class)->create();

        // act
        $this->service->saveContacts($group->id, []);

        // assert
        $this->assertDatabaseMissing('contacts', ['group_id' => $group->id]);
    }

    /**
     * @test
     */
    public function touch()
    {
        $this->markTestSkipped();

        // arrange
        Carbon::setTestNow('2018-06-10 00:00:00');
        $group = factory(Group::class)->create(['updated_at' => '2018-01-01 00:00:00']);

        // act
        $this->service->touch($group['source_id']);

        // assert
        $this->assertDatabaseHas('groups', [
            'source_id' => $group['source_id'],
            'updated_at' => '2018-06-10 00:00:00',
        ]);
    }
}
