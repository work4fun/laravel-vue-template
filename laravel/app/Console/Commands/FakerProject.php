<?php

namespace App\Console\Commands;

use App\Project;
use App\ProjectStatistics;
use Illuminate\Console\Command;

class FakerProject extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'faker:project';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Faker a demo data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $project = factory(Project::class)->create([
            "search_fields" => "abc"
        ]);

        $sequences = ['task1', 'task2', 'task3'];

        $len = rand(1, 10);

        for ($i = 0; $i < $len; $i++) {
            factory(ProjectStatistics::class, rand(1, 10))->create([
                'field' => "fail",
                'project_id' => $project->getKey(),
                'sequence' => $sequences[array_rand($sequences)],
                'created_at' => time() - rand(0, 100) * rand(1, 10)
            ]);
        }

        $len = rand(1, 10);

        for ($i = 0; $i < $len; $i++) {
            factory(ProjectStatistics::class, rand(1, 10))->create([
                'field' => "success",
                'project_id' => $project->getKey(),
                'sequence' => $sequences[array_rand($sequences)],
                'created_at' => time() - rand(0, 100) * rand(1, 10)
            ]);
        }
    }
}
