<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Contact::class, function (Faker $faker) {
    return [
        'group_id' => factory(\App\Models\Group::class)->lazy(),
        'avatar' => $faker->imageUrl(),
        'url' => $faker->url,
        'name' => $faker->name,
        'active' => $faker->boolean,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
    ];
});
