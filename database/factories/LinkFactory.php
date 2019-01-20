<?php

declare(strict_types=1);

use Faker\Generator as Faker;

$factory->define(\App\Models\Link::class, function (Faker $faker) {
    return [
        'group_id' => factory(\App\Models\Group::class)->lazy(),
        'post_id' => factory(\App\Models\Post::class)->lazy(),
        'url' => $faker->url,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
    ];
});
