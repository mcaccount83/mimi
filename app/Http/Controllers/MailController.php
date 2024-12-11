<?php

namespace App\Http\Controllers;

use App\Mail\NewChapterWelcome;
use App\Models\Chapter;
use App\Models\Conference;
use App\Models\CoordinatorPosition;
use App\Models\Region;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use GuzzleHttp\Client;
use Illuminate\Database as DatabaseConnections;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use romanzipp\QueueMonitor\Controllers\Payloads\Metric;
use romanzipp\QueueMonitor\Controllers\Payloads\Metrics;
use romanzipp\QueueMonitor\Enums\MonitorStatus;
use romanzipp\QueueMonitor\Models\Contracts\MonitorContract;
use romanzipp\QueueMonitor\Services\QueueMonitor;

class MailController extends Controller
{
    protected $userController;

    public function __construct(UserController $userController)
    {
        $this->middleware('auth')->except('logout');
        $this->userController = $userController;
    }

    public function index(Request $request)
    {
        // Fetch pending jobs
        $pendingJobs = DB::table('jobs')->get();

        $data = $request->validate([
            'status' => ['nullable', 'numeric', Rule::in(MonitorStatus::toArray())],
            'queue' => ['nullable', 'string'],
            'name' => ['nullable', 'string'],
            'custom_data' => ['nullable', 'string'],
        ]);

        $filters = [
            'status' => isset($data['status']) ? (int) $data['status'] : null,
            'queue' => $data['queue'] ?? 'all',
            'name' => $data['name'] ?? null,
            'custom_data' => $data['custom_data'] ?? null,
        ];

        $jobsQuery = QueueMonitor::getModel()->newQuery();

        if ($filters['status'] !== null) {
            $jobsQuery->where('status', $filters['status']);
        }

        if ($filters['queue'] !== 'all') {
            $jobsQuery->where('queue', $filters['queue']);
        }

        if ($filters['name'] !== null) {
            $jobsQuery->where('name', 'like', "%{$filters['name']}%");
        }

        if ($filters['custom_data'] !== null) {
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

        return view('admin.jobs', [
            'pendingJobs' => $pendingJobs,
            'jobs' => $jobs,
            'filters' => $filters,
            'queues' => $queues,
            'metrics' => $metrics,
            'statuses' => MonitorStatus::toNamedArray(),
        ]);
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

        if ($aggregatedInfo === null || $aggregatedComparisonInfo === null) {
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

    /**
     * New Chapter Welcome Email
     */
    public function createNewChapterEmail(Request $request, $id): RedirectResponse
    {
        // Find the coordinator details for the current user to be signer of letter
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
        $userName = $corDetails['first_name'].' '.$corDetails['last_name'];
        $userEmail = $corDetails['email'];
        $reg = $corDetails['region_id'];
        $conf = $corDetails['conference_id'];
        $positionId = $corDetails['position_id'];
        $position = CoordinatorPosition::find($positionId);
        $positionTitle = $position['long_title'];
        $conference = Conference::find($conf);
        $conf_name = $conference['conference_name'];
        $region = Region::find($reg);
        $reg_name = $region['long_name'];

        DB::beginTransaction();
        try {
            $chapterDetails = DB::table('chapters')
                ->select('chapters.id as id', 'chapters.name as chapter_name', 'chapters.ein as ein', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cd.email as cor_email',
                    'st.state_short_name as state', 'bd.first_name as pres_fname', 'bd.last_name as pres_lname', 'bd.email as pres_email',
                    'chapters.primary_coordinator_id as pcid')
                ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
                ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
                ->leftJoin('conference as cf', 'chapters.conference', '=', 'cf.id')
                ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
                ->where('bd.board_position_id', '=', '1')
                ->where('chapters.id', '=', $id)
                ->get();

            $chapter = $chapterDetails[0]->chapter_name;
            $state = $chapterDetails[0]->state;
            $ein = $chapterDetails[0]->ein;
            $firstName = $chapterDetails[0]->pres_fname;
            $lastName = $chapterDetails[0]->pres_lname;
            $email = $chapterDetails[0]->pres_email;
            $cor_fname = $chapterDetails[0]->cor_f_name;
            $cor_lname = $chapterDetails[0]->cor_l_name;
            $cor_email = $chapterDetails[0]->cor_email;

            // Call the getCCMemail function
            $pcid = $chapterDetails[0]->pcid;
            $cc_string = $this->userController->getCoordMail($pcid);

            $to_email = [$email];
            $cc_email = $cc_string;

            $mailData = [
                'chapter' => $chapter,
                'state' => $state,
                'ein' => $ein,
                'firstName' => $firstName,
                'lastName' => $lastName,
                'email' => $email,
                'cor_fname' => $cor_fname,
                'cor_lname' => $cor_lname,
                'cor_email' => $cor_email,
                'userName' => $userName,
                'userEmail' => $userEmail,
                'positionTitle' => $positionTitle,
                'conf' => $conf,
                'conf_name' => $conf_name,
                'reg_name' => $reg_name,
            ];

            // PDF for Group Exemption Letter
            $pdfPath2 = 'https://drive.google.com/uc?export=download&id=1A3Z-LZAgLm_2dH5MEQnBSzNZEhKs5FZ3';
            $pdfPath = $this->generateAndSaveGoodStandingLetter($id);   // Generate and save the PDF

            Mail::to($to_email)
                ->cc($cc_email)
                ->queue(new NewChapterWelcome($mailData, $pdfPath, $pdfPath2));

            DB::commit();

            return redirect()->back()->with('success', 'Welcome letter has been successfully sent');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error sending welcome letter:', ['error' => $e->getMessage()]);

            return redirect()->back()->with('fail', 'Something went wrong, Please try again.');
        }
    }

    public function generateAndSaveGoodStandingLetter($id)
    {
        $chapterDetails = DB::table('chapters')
            ->select('chapters.id as id', 'chapters.name as chapter_name', 'chapters.ein as ein', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name',
                'st.state_short_name as state', 'bd.first_name as pres_fname', 'bd.last_name as pres_lname', 'chapters.conference as conf',
                'cf.conference_name as conf_name', 'cf.conference_description as conf_desc', 'chapters.primary_coordinator_id as pcid')
            ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('conference as cf', 'chapters.conference', '=', 'cf.id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->where('chapters.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->where('chapters.id', '=', $id)
            ->get();

        $googleDrive = DB::table('google_drive')
            ->select('google_drive.good_standing_letter as good_standing_letter')
            ->get();
        $goodStandingDrive = $googleDrive[0]->good_standing_letter;

        // Load Conference Coordinators
        $chName = $chapterDetails[0]->chapter_name;
        $chState = $chapterDetails[0]->state;
        $chConf = $chapterDetails[0]->conf;
        $chPcid = $chapterDetails[0]->pcid;

        $coordinatorData = $this->userController->loadConferenceCoord($chConf, $chPcid);
        $cc_fname = $coordinatorData['cc_fname'];
        $cc_lname = $coordinatorData['cc_lname'];
        $cc_pos = $coordinatorData['cc_pos'];

        $sanitizedChapterName = str_replace(['/', '\\'], '-', $chapterDetails[0]->chapter_name);

        $pdfData = [
            'chapter_name' => $chapterDetails[0]->chapter_name,
            'state' => $chapterDetails[0]->state,
            'conf_name' => $chapterDetails[0]->conf_name,
            'conf_desc' => $chapterDetails[0]->conf_desc,
            'ein' => $chapterDetails[0]->ein,
            'pres_fname' => $chapterDetails[0]->pres_fname,
            'pres_lname' => $chapterDetails[0]->pres_lname,
            'cc_fname' => $cc_fname,
            'cc_lname' => $cc_lname,
            'cc_pos' => $cc_pos,
            'ch_name' => $sanitizedChapterName,
        ];

        $pdf = Pdf::loadView('pdf.chapteringoodstanding', compact('pdfData'));

        $chapterName = str_replace('/', '', $pdfData['chapter_name']); // Remove any slashes from chapter name
        $filename = $pdfData['state'].'_'.$chapterName.'_ChapterInGoodStanding.pdf'; // Use sanitized chapter name

        $pdfPath = storage_path('app/pdf_reports/'.$filename);
        $pdf->save($pdfPath);

        $googleClient = new Client;
        $client_id = \config('services.google.client_id');
        $client_secret = \config('services.google.client_secret');
        $refresh_token = \config('services.google.refresh_token');
        $response = Http::post('https://oauth2.googleapis.com/token', [
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'refresh_token' => $refresh_token,
            'grant_type' => 'refresh_token',
        ]);

        $chapterName = str_replace('/', '', $pdfData['chapter_name']); // Remove any slashes from chapter name
        $accessToken = json_decode((string) $response->getBody(), true)['access_token'];

        // $sharedDriveId = '1PlBi8BE2ESqUbLPTkQXzt1dKhwonyU_9';   //Shared Drive -> Disband Letters
        $sharedDriveId = $goodStandingDrive;

        // Set parent IDs for the file
        $fileMetadata = [
            'name' => $filename,
            'mimeType' => 'application/pdf',
            'parents' => [$sharedDriveId],
        ];

        // Upload the file
        $fileContent = file_get_contents($pdfPath);
        $fileContentBase64 = base64_encode($fileContent);
        $metadataJson = json_encode($fileMetadata);

        $response = $googleClient->request('POST', 'https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart&supportsAllDrives=true', [
            'headers' => [
                'Authorization' => 'Bearer '.$accessToken,
                'Content-Type' => 'multipart/related; boundary=foo_bar_baz',
            ],
            'body' => "--foo_bar_baz\r\nContent-Type: application/json; charset=UTF-8\r\n\r\n{$metadataJson}\r\n--foo_bar_baz\r\nContent-Type: {$fileMetadata['mimeType']}\r\nContent-Transfer-Encoding: base64\r\n\r\n{$fileContentBase64}\r\n--foo_bar_baz--",
        ]);

        if ($response->getStatusCode() === 200) { // Check for a successful status code
            // $pdf_file_id = json_decode($response->getBody()->getContents(), true)['id'];
            // $chapter = Chapter::find($id);
            // $chapter->disband_letter_path = $pdf_file_id;
            // $chapter->save();

            return $pdfPath;  // Return the full local stored path
        }
    }
}
