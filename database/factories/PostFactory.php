<?php

declare(strict_types=1);

use Faker\Generator as Faker;

$factory->define(\App\Models\Post::class, function (Faker $faker) {
    return [
        'group_id' => factory(\App\Models\Group::class)->lazy(),
        'post_id' => $faker->unique()->numberBetween(1, 10000000),
        'date' => $faker->dateTimeBetween('2010-01-01 00:00:00'),
        'likes' => $faker->randomDigitNotNull,
        'shares' => $faker->randomDigitNotNull,
        'views' => $faker->randomDigitNotNull,
        'comments' => $faker->randomDigitNotNull,
        'links' => $faker->randomDigitNotNull,
        'is_pinned' => $faker->boolean,
        'is_ad' => $faker->boolean,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
    ];
});
