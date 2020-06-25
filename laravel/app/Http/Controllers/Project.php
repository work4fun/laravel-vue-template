<?php

namespace App\Http\Controllers;

use App\Project as ProjectModel;
use App\ProjectStatistics;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class Project extends Controller
{
    public function createOrUpdateProject(Request $request)
    {
        // name
        // statistics
        //  - timestamps
        //  - name
        //  - sequence
        $project_data = json_decode($request->input("project"), true);

        if (empty($project_data['name'])) {
            return new Response("缺少project name", 500);
        }

        $project = [
            'project_name' => $project_data['name'],
            'search_fields' => "1"
        ];

        $statistics = $project_data['statistics'];

        if (!collect($statistics)->every('sequence')) {
            return new Response("数据不完整 缺少 sequence", 500);
        }

        DB::transaction(function () use ($project, $statistics) {
            $project = ProjectModel::updateOrCreate([
                "project_name" => $project["project_name"]
            ], $project);

            ProjectStatistics::insert(
                collect($statistics)->map(function ($item) use ($project) {
                    $rv = [
                        'field' => $item['name'],
                        'sequence' => $item['sequence'],
                        'value' => 1,
                        'project_id' => $project->getKey(),
                        'created_at' => date("Y-m-d H:i:s", time()),
                        'updated_at' => date("Y-m-d H:i:s", time()),
                    ];

                    if (!empty($item['timestamps'])) {
                        $rv['created_at'] = date("Y-m-d H:i:s", $item['timestamps']);
                        $rv['updated_at'] = date("Y-m-d H:i:s", $item['timestamps']);
                    }

                    return $rv;
                })->values()->toArray()
            );
        }, 3);

        return [
            'state' => 'ok'
        ];
    }

    public function deleteProject(Request $request)
    {
        $project_name = $request->input("project_name");

        $project = ProjectModel::where("project_name", $project_name)->first();

        if (empty($project)) {
            return new Response("项目不存在", 404);
        }

        DB::transaction(function () use ($project) {
            ProjectModel::where("id", $project->getKey())->delete();
            ProjectStatistics::where("project_id", $project->getKey())->delete();
        }, 3);

        return [
            'state' => 'ok'
        ];
    }

    public function search(Request $request)
    {
        $field = $request->get("field");
        $times = $request->get("times");
        $start_time = $request->get("start_time");
        $end_time = $request->get("end_time");

        $project_name = $request->get('project_name');

        if (empty($start_time) || empty($end_time)) {
            return new Response("请选择时间区间", 500);
        }

        $builder = ProjectModel::with(['statistics' => function ($q) use ($start_time, $end_time) {
            $q->where("created_at", ">=", date("Y-m-d H:i:s", $start_time));
            $q->where("created_at", "<=", date("Y-m-d H:i:s", $end_time));
        }]);

        if (!empty($project_name)) {
            $builder = $builder->where('project_name', 'like', "${project_name}%");
        }

        $projects = $builder->get();

        $projects = $projects->filter(function ($project) use ($field, $times) {
            $statistics_groupbyed = $project->statistics->groupBy('field');

            // no fields
            if (count($statistics_groupbyed) <= 0) {
                return false;
            }

            // no query
            if (empty($field)) {
                return true;
            }

            if (empty($statistics_groupbyed[$field])) {
                return false;
            }

            return count($statistics_groupbyed[$field]) >= $times;
        })->values();

        return $projects;
    }

    public function projectNames(Request $request) {
        $start_time = $request->get("start_time");
        $end_time = $request->get("end_time");

        if (empty($start_time)
            || empty($end_time)
        ) {
            return new Response("请选择时间区间", 500);
        }

        $builder = ProjectModel::whereHas('statistics', function ($q) use ($start_time, $end_time) {
            $q->where("created_at", ">=", date("Y-m-d H:i:s", $start_time));
            $q->where("created_at", "<=", date("Y-m-d H:i:s", $end_time));
        });

        return $builder->pluck('project_name');
    }

    public function truncateall(Request $request) {
        DB::transaction(function () {
            ProjectModel::truncate();
            ProjectStatistics::truncate();
        }, 3);

        return [
            'state' => 'ok'
        ];
    }

    public function index()
    {
        $start_time = time() - 60 * 15;
        $end_time = time();

        $builder = ProjectModel::with(['statistics' => function ($q) use ($start_time, $end_time) {
            $q->where("created_at", ">=", date("Y-m-d H:i:s", $start_time));
            $q->where("created_at", "<=", date("Y-m-d H:i:s", $end_time));
        }]);

        $projects = $builder->get();

        $projects = $projects->filter(function ($project) {
            $statistics_groupbyed = $project->statistics->groupBy('field');
            return count($statistics_groupbyed) > 0;
        })->values();

        return view('index', [
            'projects' => $projects->toArray()
        ]);
    }
}
