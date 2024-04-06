<?php

namespace App\Http\Controllers;

use App\Data\Statistics\Chart;
use App\Data\Statistics\ChartData;
use App\Data\Statistics\Dataset;
use App\Data\Statistics\Stat;
use App\Enums\ChartType;
use App\Models\Academic;
use App\Models\Comment;
use App\Models\Department;
use App\Models\Idea;
use App\Models\Staff;
use Exception;
use Spatie\Analytics\Facades\Analytics;
use Spatie\Analytics\Period;

class StatisticsController extends Controller
{
    public function __invoke()
    {
        // $startDate = now()->subYear();
        // $endDate = now();

        // $period = Period::create($startDate, $endDate);

        // // $fetchVisitorsAndPageViews = Analytics::fetchVisitorsAndPageViews($period);
        // // $fetchTotalVisitorsAndPageViews = Analytics::fetchTotalVisitorsAndPageViews($period);
        // $fetchMostVisitedPages = Analytics::fetchMostVisitedPages($period);
        // $mostActiveUsers = Staff::orderBy('last_logged_in_at', 'desc')->limit(10)->get();
        // $fetchTopBrowsers = Analytics::fetchTopBrowsers($period);

        // dd(
        //     $fetchMostVisitedPages,
        //     $fetchUserTypes,
        //     $fetchTopBrowsers
        // );

        $academic = Academic::isActive()->first() ?? Academic::isPrevious()->first();

        if (! $academic) {
            throw new Exception('There is no active academic or previous academic');
        }

        $departments = Department::withCount('ideas', 'staffs')->get();
        $numberOfIdeaPerDepartment = [
            'labels' => [],
            'datasets' => [
                Dataset::from([
                    'label' => 'Number of ideas',
                    'data' => [],
                    'backgroundColor' => [],
                ]),
            ],
        ];
        $percentageOfIdeaPerDepartment = [
            'labels' => [],
            'datasets' => [
                Dataset::from([
                    'label' => 'Number of ideas',
                    'data' => [],
                    'backgroundColor' => [],
                ]),
            ],
        ];

        foreach ($departments as $department) {
            $totalIdeas = Idea::where('department_id', $department->id)->count();

            $numberOfIdeaPerDepartment['labels'][] = $department->name;
            $numberOfIdeaPerDepartment['datasets'][0]->data[] = $totalIdeas;
            $numberOfIdeaPerDepartment['datasets'][0]->backgroundColor[] = $department->color_code;

            $totalIdeasAcrossAllDepartments = Idea::count();
            $percentage = ($totalIdeasAcrossAllDepartments > 0) ? ($totalIdeas / $totalIdeasAcrossAllDepartments) * 100 : 0;
            $percentageOfIdeaPerDepartment['labels'][] = $department->name;
            $percentageOfIdeaPerDepartment['datasets'][0]->data[] = round($percentage, 2);
            $percentageOfIdeaPerDepartment['datasets'][0]->backgroundColor[] = $department->color_code;
        }

        return $this->responseSuccess([
            'charts' => [
                Chart::from([
                    'label' => 'Number of ideas made by each Department',
                    'type' => ChartType::BAR,
                    'data' => ChartData::from($numberOfIdeaPerDepartment),
                ]),
                Chart::from([
                    'label' => 'Percentage of ideas by each Department',
                    'type' => ChartType::DOUGHNUT,
                    'data' => ChartData::from($percentageOfIdeaPerDepartment),
                ]),
            ],
            'stats' => [
                'ideas_count_without_comment' => Stat::from([
                    'label' => 'Ideas without comment',
                    'value' => number_format(Idea::whereDoesntHave('comments')->count()),
                    'path' => '/ideas?without_comment=1',
                ]),
                'anonymous_ideas_count' => Stat::from([
                    'label' => 'Anonymous Ideas',
                    'value' => number_format(Idea::where('is_anonymous', 1)->count()),
                    'path' => '/ideas?anonymous=1',
                ]),
                'anonymous_comments_count' => Stat::from([
                    'label' => 'Anonymous Comments',
                    'value' => number_format(Comment::where('is_anonymous', 1)->count()),
                ]),
            ],
        ]);
    }
}
