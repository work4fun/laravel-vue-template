<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\ProjectStatistics;
use App\Project;
use Faker\Generator as Faker;

$factory->define(ProjectStatistics::class, function (Faker $faker) {
    $project = factory(Project::class)->create();
    return [
        "project_id" => $project->getKey(),
        "field" => "fail",
        "value" => 1
    ];
});
