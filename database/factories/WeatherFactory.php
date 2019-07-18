<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Weather;
use Faker\Generator as Faker;

$factory->define(Weather::class, function (Faker $faker) {
    return [
        "id" => $faker->unique()->numberBetween($min = 1, $max = 30),
        "date" => $faker->date($format = 'Y-m-d', $max = 'now'),
        "location" => [
            "lat" => $faker->numberBetween($min = -1000, $max = 1000),
            "lon" => $faker->numberBetween($min = -1000, $max = 1000),
            "city" => $faker->city,
            "state" => $faker->state,
        ],
        "temperature" => [
            28.5, 27.6, 26.7, 25.9, 25.3, 24.7,
            24.3, 24.0, 27.1, 34.0, 38.6, 41.3,
            43.2, 44.4, 45.0, 45.3, 45.1, 44.2,
            41.9, 38.0, 35.0, 33.0, 31.1, 29.9
        ]
    ];
});
