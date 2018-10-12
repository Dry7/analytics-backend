<?php

declare(strict_types=1);

use Faker\Generator as Faker;

$factory->define(\App\Models\Group::class, function (Faker $faker) {
    $typeId = $faker->numberBetween(1, 3);
    return [
        'network_id' => $faker->numberBetween(1, 4),
        'type_id' => $typeId,
        'avatar' => $faker->imageUrl(),
        'title' => $faker->title,
        'source_id' => $faker->unique()->numberBetween(1, 160000000),
        'slug' => $faker->unique()->slug,
        'members' => $faker->numberBetween(0, 1000000),
        'is_verified' => $faker->boolean,
        'is_closed' => $faker->boolean,
        'is_adult' => $faker->boolean,
        'is_banned' => $faker->boolean,
        'in_search' => $faker->boolean,
        'posts' => $faker->numberBetween(0, 100000),
        'posts_links' => $faker->numberBetween(0, 2000),
        'ads' => $faker->numberBetween(0, 1000),
        'likes' => $faker->numberBetween(0, 10000000),
        'shares' => $faker->numberBetween(0, 10000000),
        'comments' => $faker->numberBetween(0, 10000000),
        'avg_posts' => $faker->numberBetween(0, 10),
        'avg_comments_per_post' => $faker->numberBetween(0, 10),
        'avg_likes_per_post' => $faker->numberBetween(0, 10),
        'avg_shares_per_post' => $faker->numberBetween(0, 10),
        'avg_views_per_post' => $faker->numberBetween(0, 10),
        'members_day_inc' => $faker->numberBetween(0, 10000),
        'members_day_inc_percent' => $faker->randomFloat(2, 0, 1000),
        'members_month_inc' => $faker->numberBetween(0, 10000),
        'members_week_inc_percent' => $faker->randomFloat(2, 0, 1000),
        'country_code' => $faker->randomElement([null, 'RU', 'US', 'UA']),
        'state_code' => $faker->randomElement([null, 'RU-MOW']),
        'city_code' => $faker->randomElement([null, 524901]),
        'opened_at' => $faker->dateTimeBetween('2010-01-01'),
        'last_post_at' => $faker->dateTimeBetween('2010-01-01'),
        'event_start' => $typeId === \App\Types\Type::EVENT ? $faker->dateTimeBetween('2010-01-01') : null,
        'event_end' => $typeId === \App\Types\Type::EVENT ? $faker->dateTimeBetween('2010-01-01') : null,
        'cpp' => $faker->numberBetween(0, 2000),
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
    ];
});
