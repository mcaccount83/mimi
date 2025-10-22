<?php

namespace App\Http\Controllers;

use Illuminate\Database as DatabaseConnections;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use romanzipp\QueueMonitor\Controllers\Payloads\Metric;
use romanzipp\QueueMonitor\Controllers\Payloads\Metrics;
use romanzipp\QueueMonitor\Enums\MonitorStatus;
use romanzipp\QueueMonitor\Models\Contracts\MonitorContract;
use romanzipp\QueueMonitor\Services\QueueMonitor;

class MailController extends Controller implements HasMiddleware
{
    protected $userController;

    public function __construct(UserController $userController)
    {

        $this->userController = $userController;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('auth', except: ['logout']),
        ];
    }

    public function index(Request $request)
    {
        try {
            // Fetch pending jobs
            $pendingJobs = DB::table('jobs')->get();

            $data = $request->validate([
                'status' => [
                    'nullable',
                    'numeric',
                    Rule::in(MonitorStatus::toArray()),
                ],
                'queue' => 'nullable|string',
                'name' => 'nullable|string',
                'custom_data' => 'nullable|string',
            ]);

            $filters = [
                'status' => isset($data['status']) ? (int) $data['status'] : null,
                'queue' => $data['queue'] ?? 'all',
                'name' => $data['name'] ?? null,
                'custom_data' => $data['custom_data'] ?? null,
            ];

            $jobsQuery = QueueMonitor::getModel()->newQuery();

            if ($filters['status'] != null) {
                $jobsQuery->where('status', $filters['status']);
            }

            if ($filters['queue'] != 'all') {
                $jobsQuery->where('queue', $filters['queue']);
            }

            if ($filters['name'] != null) {
                $jobsQuery->where('name', 'like', "%{$filters['name']}%");
            }

            if ($filters['custom_data'] != null) {
                $jobsQuery->where('data', 'like', "%{$filters['custom_data']}%");
            }

            $connection = DB::connection();
            if (config('queue-monitor.ui.order_queued_first')) {
                if ($connection instanceof DatabaseConnections\MySqlConnection) {
                    $jobsQuery->orderByRaw('-`started_at`');
                }

                if ($connection instanceof DatabaseConnections\SqlServerConnection) {
                    $jobsQuery->orderByRaw('(CASE WHEN [started_at] IS NULL THEN 0 ELSE 1 END)');
                }

                if ($connection instanceof DatabaseConnections\SQLiteConnection) {
                    $jobsQuery->orderByRaw('started_at DESC NULLS FIRST');
                }
            } elseif ($connection instanceof DatabaseConnections\PostgresConnection) {
                $jobsQuery->orderByRaw('started_at DESC NULLS LAST');
            }

            $jobsQuery
                ->orderByDesc('started_at')
                ->orderByDesc('started_at_exact');

            $jobs = $jobsQuery
                ->paginate(config('queue-monitor.ui.per_page'))
                ->appends(
                    $request->all()
                );

            $queues = QueueMonitor::getModel()
                ->newQuery()
                ->select('queue')
                ->groupBy('queue')
                ->get()
                ->map(function (MonitorContract $monitor) {
                    /** @var \romanzipp\QueueMonitor\Models\Monitor $monitor */
                    return $monitor->queue;
                })
                ->toArray();

            $metrics = null;

            if (config('queue-monitor.ui.show_metrics')) {
                $metrics = $this->collectMetrics();
            }

            return view('techreports.jobs', [
                'pendingJobs' => $pendingJobs,
                'jobs' => $jobs,
                'filters' => $filters,
                'queues' => $queues,
                'metrics' => $metrics,
                'statuses' => MonitorStatus::toNamedArray(),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while loading the jobs page.');
        }
    }

    public function collectMetrics(): Metrics
    {
        $timeFrame = config('queue-monitor.ui.metrics_time_frame') ?? 2;

        $metrics = new Metrics;

        $connection = DB::connection();

        $expressionTotalTime = DB::raw('SUM(TIMESTAMPDIFF(SECOND, `started_at`, `finished_at`)) as `total_time_elapsed`');
        $expressionAverageTime = DB::raw('AVG(TIMESTAMPDIFF(SECOND, `started_at`, `finished_at`)) as `average_time_elapsed`');

        if ($connection instanceof DatabaseConnections\SQLiteConnection) {
            $expressionTotalTime = DB::raw('SUM(strftime("%s", `finished_at`) - strftime("%s", `started_at`)) as total_time_elapsed');
            $expressionAverageTime = DB::raw('AVG(strftime("%s", `finished_at`) - strftime("%s", `started_at`)) as average_time_elapsed');
        }

        if ($connection instanceof DatabaseConnections\SqlServerConnection) {
            $expressionTotalTime = DB::raw('SUM(DATEDIFF(SECOND, "started_at", "finished_at")) as "total_time_elapsed"');
            $expressionAverageTime = DB::raw('AVG(DATEDIFF(SECOND, "started_at", "finished_at")) as "average_time_elapsed"');
        }

        if ($connection instanceof DatabaseConnections\PostgresConnection) {
            $expressionTotalTime = DB::raw('SUM(EXTRACT(EPOCH FROM (finished_at - started_at))) as total_time_elapsed');
            $expressionAverageTime = DB::raw('AVG(EXTRACT(EPOCH FROM (finished_at - started_at))) as average_time_elapsed');
        }

        $aggregationColumns = [
            DB::raw('COUNT(*) as count'),
            $expressionTotalTime,
            $expressionAverageTime,
        ];

        $aggregatedInfo = QueueMonitor::getModel()
            ->newQuery()
            ->select($aggregationColumns)
            ->where('status', '!=', MonitorStatus::RUNNING)
            ->where('started_at', '>=', Carbon::now()->subDays($timeFrame))
            ->first();

        $aggregatedComparisonInfo = QueueMonitor::getModel()
            ->newQuery()
            ->select($aggregationColumns)
            ->where('status', '!=', MonitorStatus::RUNNING)
            ->where('started_at', '>=', Carbon::now()->subDays($timeFrame * 2))
            ->where('started_at', '<=', Carbon::now()->subDays($timeFrame))
            ->first();

        if ($aggregatedInfo == null || $aggregatedComparisonInfo == null) {
            return $metrics;
        }

        return $metrics
            ->push(
                new Metric('Total Jobs Executed', $aggregatedInfo->count ?? 0, $aggregatedComparisonInfo->count, '%d')
            )
            ->push(
                new Metric('Total Execution Time', $aggregatedInfo->total_time_elapsed ?? 0, $aggregatedComparisonInfo->total_time_elapsed, '%ds')
            )
            ->push(
                new Metric('Average Execution Time', $aggregatedInfo->average_time_elapsed ?? 0, $aggregatedComparisonInfo->average_time_elapsed, '%0.2fs')
            );
    }
}
