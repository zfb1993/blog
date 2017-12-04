<?php

use Faker\Generator as Faker;

/* @var Illuminate\Database\Eloquent\Factory $factory */

$factory->define(Model::class, function (Faker $faker) {
    date_time = $faker->date . ' ' . $faker->time;
    return [
      'content' => $faker->text(),
      'created_at' => $date_time,
      'updated_at' => $date_time,
    ];
});
