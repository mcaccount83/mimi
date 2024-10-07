<?php

namespace App\Http\Controllers;

use App\Mail\BigSisterWelcome;
use App\Mail\NewChapterWelcome;
use App\Mail\PaymentsReRegLate;
use App\Mail\PaymentsReRegReminder;
use App\Models\Chapter;
use App\Models\CoordinatorPosition;
use App\Models\Conference;
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
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        //$this->middleware('preventBackHistory');
        $this->middleware('auth');
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

        if (null !== $filters['status']) {
            $jobsQuery->where('status', $filters['status']);
        }

        if ('all' !== $filters['queue']) {
            $jobsQuery->where('queue', $filters['queue']);
        }

        if (null !== $filters['name']) {
            $jobsQuery->where('name', 'like', "%{$filters['name']}%");
        }

        if (null !== $filters['custom_data']) {
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
            ->orderBy('started_at', 'desc')
            ->orderBy('started_at_exact', 'desc');

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

        $metrics = new Metrics();

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

        if (null === $aggregatedInfo || null === $aggregatedComparisonInfo) {
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
     * Downline for Emailing Chapter and Coordintors
     */
    public function getEmailDetails($id)
    {
        $chapterList = DB::table('chapters')
            ->select('chapters.id as id', 'chapters.primary_coordinator_id as primary_coordinator_id', 'chapters.financial_report_received as report_received',
                'chapters.new_board_submitted as board_submitted', 'chapters.ein_letter as ein_letter', 'chapters.name as name', 'st.state_short_name as state')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->where('chapters.id', '=', $id)
            ->first();

        $chapterEmailList = DB::table('boards as bd')
            ->select('bd.email as bor_email')
            ->where('bd.chapter_id', '=', $chapterList->id)
            ->get();

        $emailListCord = '';
        foreach ($chapterEmailList as $val) {
            $email = $val->bor_email;
            $escaped_email = str_replace("'", "\\'", $email);
            if ($emailListCord == '') {
                $emailListCord = $escaped_email;
            } else {
                $emailListCord .= ';'.$escaped_email;
            }
        }
        $cc_string = '';
        $reportingList = DB::table('coordinator_reporting_tree')
            ->select('*')
            ->where('id', '=', $chapterList->primary_coordinator_id)
            ->get();
        foreach ($reportingList as $key => $value) {
            $reportingList[$key] = (array) $value;
        }
        $filterReportingList = array_filter($reportingList[0]);
        unset($filterReportingList['id']);
        unset($filterReportingList['layer0']);
        $filterReportingList = array_reverse($filterReportingList);
        $str = '';
        $array_rows = count($filterReportingList);
        $down_line_email = '';
        foreach ($filterReportingList as $key => $val) {
            //if($corId != $val && $val >1){
            if ($val > 1) {
                $corList = DB::table('coordinators as cd')
                    ->select('cd.email as cord_email')
                    ->where('cd.id', '=', $val)
                    ->where('cd.is_active', '=', 1)
                    ->get();
                if ($down_line_email == '') {
                    if (isset($corList[0])) {
                        $down_line_email = $corList[0]->cord_email;
                    }
                } else {
                    if (isset($corList[0])) {
                        $down_line_email .= ';'.$corList[0]->cord_email;
                    }
                }

            }
        }
        $cc_string = '?cc='.$down_line_email;

        return [
            'emailListCord' => $emailListCord,
            'cc_string' => $cc_string,
            'board_submitted' => $chapterList->board_submitted,
            'report_received' => $chapterList->report_received,
            'ein_letter' => $chapterList->ein_letter,
            'name' => $chapterList->name,
            'state' => $chapterList->state,
        ];

    }

    /**
     * get Coordinator Downline for CCMail
     */
    public function getCCMail($pcid)
    {
        $reportingList = DB::table('coordinator_reporting_tree')
            ->select('*')
            ->where('id', '=', $pcid)
            ->get();
        foreach ($reportingList as $key => $value) {
            $reportingList[$key] = (array) $value;
        }
        $filterReportingList = array_filter($reportingList[0]);
        unset($filterReportingList['id']);
        unset($filterReportingList['layer0']);
        $filterReportingList = array_reverse($filterReportingList);
        $str = '';
        $array_rows = count($filterReportingList);
        $down_line_email = [];
        foreach ($filterReportingList as $key => $val) {
            //if($corId != $val && $val >1){
            if ($val > 1) {
                $corList = DB::table('coordinators as cd')
                    ->select('cd.email as cord_email')
                    ->where('cd.id', '=', $val)
                    ->where('cd.is_active', '=', 1)
                    ->get();
                if (count($corList) > 0) {
                    if ($down_line_email == '') {
                        $down_line_email[] = $corList[0]->cord_email;
                    } else {
                        $down_line_email[] = $corList[0]->cord_email;
                    }
                }
            }
        }

        return $down_line_email;
    }


    /**
     * Load Conference Coordinators if emailing only CC for each chapter
     */
    public function load_cc_coordinators($chConf, $chPcid)
    {
        $reportingList = DB::table('coordinator_reporting_tree')
            ->select('*')
            ->where('id', '=', $chPcid)
            ->get();

        foreach ($reportingList as $key => $value) {
            $reportingList[$key] = (array) $value;
        }
        $filterReportingList = array_filter($reportingList[0]);
        unset($filterReportingList['id']);
        unset($filterReportingList['layer0']);
        $filterReportingList = array_reverse($filterReportingList);
        $str = '';
        $array_rows = count($filterReportingList);
        $i = 0;
        $coordinator_array = [];
        foreach ($filterReportingList as $key => $val) {
            $corList = DB::table('coordinators as cd')
                ->select('cd.id as cid', 'cd.first_name as fname', 'cd.last_name as lname', 'cp.long_title as pos', 'cd.email as email',
                    'cd.conference_id as conf', 'cf.conference_description as conf_desc', )
                ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
                ->leftJoin('conference as cf', 'cd.conference_id', '=', 'cf.id')
                ->where('cd.id', '=', $val)
                ->get();
            $coordinator_array[$i] = ['id' => $corList[0]->cid,
                'first_name' => $corList[0]->fname,
                'last_name' => $corList[0]->lname,
                'pos' => $corList[0]->pos,
                'conf' => $corList[0]->conf,
                'conf_desc' => $corList[0]->conf_desc,
                'email' => $corList[0]->email,
            ];

            $i++;
        }
        $coordinator_count = count($coordinator_array);

        for ($i = 0; $i < $coordinator_count; $i++) {
            $cc_fname = $coordinator_array[$i]['first_name'];
            $cc_lname = $coordinator_array[$i]['last_name'];
            $cc_pos = $coordinator_array[$i]['pos'];
            $cc_conf = $coordinator_array[$i]['conf'];
            $cc_conf_desc = $coordinator_array[$i]['conf_desc'];
            $cc_email = $coordinator_array[$i]['email'];
        }

        switch ($chConf) {
            case 1:   //Conference 1
                $cc_fname = $cc_fname;
                $cc_lname = $cc_lname;
                $cc_pos = $cc_pos;
                $cc_conf = $cc_conf;
                $cc_conf_desc = $cc_conf_desc;
                $cc_email = $cc_email;
                break;
            case 2:  //Conference 2
                $cc_fname = $cc_fname;
                $cc_lname = $cc_lname;
                $cc_pos = $cc_pos;
                $cc_conf = $cc_conf;
                $cc_conf_desc = $cc_conf_desc;
                $cc_email = $cc_email;
                break;
            case 3:  //Conference 3
                $cc_fname = $cc_fname;
                $cc_lname = $cc_lname;
                $cc_pos = $cc_pos;
                $cc_conf = $cc_conf;
                $cc_conf_desc = $cc_conf_desc;
                $cc_email = $cc_email;
                break;
            case 4:  //Conference 4
                $cc_fname = $cc_fname;
                $cc_lname = $cc_lname;
                $cc_pos = $cc_pos;
                $cc_conf = $cc_conf;
                $cc_conf_desc = $cc_conf_desc;
                $cc_email = $cc_email;
                break;
            case 5:  //Conference 5
                $cc_fname = $cc_fname;
                $cc_lname = $cc_lname;
                $cc_pos = $cc_pos;
                $cc_conf = $cc_conf;
                $cc_conf_desc = $cc_conf_desc;
                $cc_email = $cc_email;
                break;
        }

        return [
            'cc_fname' => $cc_fname,
            'cc_lname' => $cc_lname,
            'cc_pos' => $cc_pos,
            'cc_conf' => $cc_conf,
            'cc_conf_desc' => $cc_conf_desc,
            'cc_email' => $cc_email,
            // 'coordinator_array' => $coordinator_array,
        ];
    }

    /**
     * Generate disbanding PDF
     */
    public function generateAndSaveDisbandLetter($chapterid)
    {
        $chapterDetails = DB::table('chapters')
            ->select('chapters.id as id', 'chapters.name as chapter_name', 'chapters.ein as ein', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name',
                'st.state_short_name as state', 'bd.first_name as pres_fname', 'bd.last_name as pres_lname', 'bd.street_address as pres_addr', 'bd.city as pres_city', 'bd.state as pres_state',
                'bd.zip as pres_zip', 'chapters.conference as conf', 'cf.conference_name as conf_name', 'cf.conference_description as conf_desc', 'chapters.primary_coordinator_id as pcid')
            ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('conference as cf', 'chapters.conference', '=', 'cf.id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            // ->where('chapters.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->where('chapters.id', '=', $chapterid)
            ->get();

        // Call the load_cc_coordinators function
        $chName = $chapterDetails[0]->chapter_name;
        $chState = $chapterDetails[0]->state;
        $chConf = $chapterDetails[0]->conf;
        $chPcid = $chapterDetails[0]->pcid;

        $coordinatorData = $this->load_cc_coordinators($chConf, $chPcid);
        $cc_fname = $coordinatorData['cc_fname'];
        $cc_lname = $coordinatorData['cc_lname'];
        $cc_pos = $coordinatorData['cc_pos'];

        $pdfData = [
            'chapter_name' => $chapterDetails[0]->chapter_name,
            'state' => $chapterDetails[0]->state,
            'conf' => $chapterDetails[0]->conf,
            'conf_name' => $chapterDetails[0]->conf_name,
            'conf_desc' => $chapterDetails[0]->conf_desc,
            'ein' => $chapterDetails[0]->ein,
            'pres_fname' => $chapterDetails[0]->pres_fname,
            'pres_lname' => $chapterDetails[0]->pres_lname,
            'pres_addr' => $chapterDetails[0]->pres_addr,
            'pres_city' => $chapterDetails[0]->pres_city,
            'pres_state' => $chapterDetails[0]->pres_state,
            'pres_zip' => $chapterDetails[0]->pres_zip,
            'cc_fname' => $cc_fname,
            'cc_lname' => $cc_lname,
            'cc_pos' => $cc_pos,
        ];

        $pdf = Pdf::loadView('pdf.disbandletter', compact('pdfData'));

        $chapterName = str_replace('/', '', $pdfData['chapter_name']); // Remove any slashes from chapter name
        $filename = $pdfData['state'].'_'.$chapterName.'_Disband_Letter.pdf'; // Use sanitized chapter name

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

        $sharedDriveId = '1PlBi8BE2ESqUbLPTkQXzt1dKhwonyU_9';   //Shared Drive -> Disband Letters

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
            $pdf_file_id = json_decode($response->getBody()->getContents(), true)['id'];
            $chapter = Chapter::find($chapterid);
            $chapter->disband_letter_path = $pdf_file_id;
            $chapter->save();

            return $pdfPath;  // Return the full local stored path
        }
    }

    /**
     * ReRegistration Reminders Auto Send
     */
    public function createReminderReRegistration(Request $request): RedirectResponse
    {
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $corName = $corDetails['first_name'].' '.$corDetails['last_name'];

        $month = date('m');
        $year = date('Y');
        $monthRangeStart = $month;
        $monthRangeEnd = $month - 1;
        $lastYear = $year - 1;
        $thisyear = $year;

        if ($month == 1) {
            $monthRangeStart = 12;
            $lastYear = $lastYear - 1;
        }
        if ($month == 1) {
            $monthRangeEnd = 12;
            $thisyear = $year - 1;
        }

        $rangeStartDate = Carbon::create($lastYear, $monthRangeStart, 1);
        $rangeEndDate = Carbon::create($thisyear, $monthRangeEnd, 1)->endOfMonth();

        // Convert $month to words
        $monthInWords = Carbon::createFromFormat('m', $month)->format('F');

        // Format dates as "mm-dd-yyyy"
        $rangeStartDateFormatted = $rangeStartDate->format('m-d-Y');
        $rangeEndDateFormatted = $rangeEndDate->format('m-d-Y');

        $chapters = Chapter::select('chapters.name as chapter_name', 'state.state_short_name as chapter_state', 'boards.email as bor_email',
            'chapters.primary_coordinator_id as pcid', 'chapters.email as ch_email', 'chapters.start_month_id as start_month',
            'boards.board_position_id')
            ->join('state', 'chapters.state', '=', 'state.id')
            ->join('boards', 'chapters.id', '=', 'boards.chapter_id')
            ->whereIn('boards.board_position_id', [1, 2, 3, 4, 5])
            ->where('chapters.conference', $corConfId)
            ->where('chapters.start_month_id', $month)
            ->where('chapters.next_renewal_year', $year)
            ->where('chapters.is_active', 1)
            ->get();

        $cc_email = [];
        $chapterEmails = []; // Store email addresses for each chapter
        $coordinatorEmails = []; // Store coordinator email addresses by chapter
        $chapterChEmails = []; // Store ch_email addresses by chapter

        $mailData = [];

        if ($chapters->isEmpty()) {
            return redirect()->back()->with('info', 'There are no Chapters with Re-Registration Reminders to be Sent this Month.');
        } else {
            foreach ($chapters as $chapter) {
                if ($chapter->bor_email) {
                    $chapterEmails[$chapter->chapter_name][] = $chapter->bor_email; // Store emails by chapter name

                    $cc_email1 = $this->getCCMail($chapter->pcid);
                    $cc_email1 = array_filter($cc_email1);

                    if (! empty($cc_email1)) {
                        $coordinatorEmails[$chapter->chapter_name] = $cc_email1; // Store coordinator emails by chapter
                    }
                }

                // Check if ch_email is not null before adding it to the chapterChEmails array
                if ($chapter->ch_email) {
                    $chapterChEmails[$chapter->chapter_name] = $chapter->ch_email;
                }

                // Set the state for this chapter
                $chapterState = $chapter->chapter_state; // Use the state for this chapter

                $mailData[$chapter->chapter_name] = [
                    'chapterName' => $chapter->chapter_name,
                    'chapterState' => $chapterState,
                    'startRange' => $rangeStartDateFormatted,
                    'endRange' => $rangeEndDateFormatted,
                    'startMonth' => $monthInWords,
                ];

                if (isset($chapterChEmails[$chapter->chapter_name])) {
                    $chapterEmails[$chapter->chapter_name][] = $chapterChEmails[$chapter->chapter_name];
                }
            }
        }

        foreach ($mailData as $chapterName => $data) {
            $toRecipients = isset($chapterEmails[$chapterName]) ? $chapterEmails[$chapterName] : [];
            $ccRecipients = isset($coordinatorEmails[$chapterName]) ? $coordinatorEmails[$chapterName] : [];

            if (! empty($toRecipients)) {
                // Split recipients into batches of 50 - so won't be over 100 after adding ccRecipients
                $toBatches = array_chunk($toRecipients, 25);

                foreach ($toBatches as $toBatch) {
                    Mail::to($toBatch)
                        ->cc($ccRecipients)
                        ->queue(new PaymentsReRegReminder($data));
                }
            }
        }

        try {
            DB::commit();
        } catch (\Exception $e) {
            // Rollback Transaction
            echo $e->getMessage();
            exit();
            // Log the error
            Log::error($e);

            return redirect()->back()->with('fail', 'Something went wrong, Please try again.');
        }

        return redirect()->to('/chapter/re-registration')->with('success', 'Re-Registration Reminders have been successfully sent.');
    }

    /**
     * ReRegistration Late Notices Auto Send
     */
    public function createLateReRegistration(Request $request): RedirectResponse
    {
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $corName = $corDetails['first_name'].' '.$corDetails['last_name'];

        $month = date('m');
        $year = date('Y');
        $lastMonth = $month - 1;
        $monthRangeStart = $month - 1;
        $monthRangeEnd = $month - 2;
        $lastYear = $year - 1;
        $thisyear = $year;

        if ($month == 1) {
            $monthRangeStart = 11;
            $lastYear = $lastYear - 1;
        } elseif ($month == 2) {
            $monthRangeStart = 12;
            $lastYear = $lastYear - 1;
        }

        if ($month == 1) {
            $monthRangeEnd = 11;
            $thisyear = $year - 1;
        } elseif ($month == 2) {
            $monthRangeEnd = 12;
            $thisyear = $year - 1;
        }

        $rangeStartDate = Carbon::create($lastYear, $monthRangeStart, 1);
        $rangeEndDate = Carbon::create($thisyear, $monthRangeEnd, 1)->endOfMonth();

        // Convert $month to words
        $monthInWords = Carbon::createFromFormat('m', $month)->format('F');
        $lastMonthInWords = Carbon::createFromFormat('m', $lastMonth)->format('F');

        // Format dates as "mm-dd-yyyy"
        $rangeStartDateFormatted = $rangeStartDate->format('m-d-Y');
        $rangeEndDateFormatted = $rangeEndDate->format('m-d-Y');

        $chapters = Chapter::select('chapters.name as chapter_name', 'state.state_short_name as chapter_state', 'boards.email as bor_email',
            'chapters.primary_coordinator_id as pcid', 'chapters.email as ch_email', 'chapters.start_month_id as start_month',
            'boards.board_position_id')
            ->join('state', 'chapters.state', '=', 'state.id')
            ->join('boards', 'chapters.id', '=', 'boards.chapter_id')
            ->whereIn('boards.board_position_id', [1, 2, 3, 4, 5])
            ->where('chapters.conference', $corConfId)
            ->where(function ($query) use ($month, $year) {
                if ($month == 1) {
                    // January, so get chapters with December start_month_id
                    $query->where('chapters.start_month_id', 12)
                        ->where('chapters.next_renewal_year', $year - 1);
                } else {
                    // Any other month, get chapters with $month - 1 start_month_id
                    $query->where('chapters.start_month_id', $month - 1)
                        ->where('chapters.next_renewal_year', $year);
                }
            })
            ->where('chapters.is_active', 1)
            ->get();

        $cc_email = [];
        $chapterEmails = []; // Store email addresses for each chapter
        $coordinatorEmails = []; // Store coordinator email addresses by chapter
        $chapterChEmails = []; // Store ch_email addresses by chapter

        $mailData = [];

        if ($chapters->isEmpty()) {
            return redirect()->back()->with('info', 'There are no Chapters with Late Reminders to be Sent this Month.');
        } else {
            foreach ($chapters as $chapter) {
                if ($chapter->bor_email) {
                    $chapterEmails[$chapter->chapter_name][] = $chapter->bor_email; // Store emails by chapter name

                    $cc_email1 = $this->getCCMail($chapter->pcid);
                    $cc_email1 = array_filter($cc_email1);

                    if (! empty($cc_email1)) {
                        $coordinatorEmails[$chapter->chapter_name] = $cc_email1; // Store coordinator emails by chapter
                    }
                }

                // Check if ch_email is not null before adding it to the chapterChEmails array
                if ($chapter->ch_email) {
                    $chapterChEmails[$chapter->chapter_name] = $chapter->ch_email;
                    $chapterEmails[$chapter->chapter_name][] = $chapter->ch_email; // Add ch_email to chapterEmails
                }

                // Set the state for this chapter
                $chapterState = $chapter->chapter_state; // Use the state for this chapter

                $mailData[$chapter->chapter_name] = [
                    'chapterName' => $chapter->chapter_name,
                    'chapterState' => $chapterState,
                    'startRange' => $rangeStartDateFormatted,
                    'endRange' => $rangeEndDateFormatted,
                    'startMonth' => $lastMonthInWords,
                    'dueMonth' => $monthInWords,
                ];

                if (isset($chapterChEmails[$chapter->chapter_name])) {
                    $chapterEmails[$chapter->chapter_name][] = $chapterChEmails[$chapter->chapter_name];
                }
            }
        }

        foreach ($mailData as $chapterName => $data) {
            $toRecipients = isset($chapterEmails[$chapterName]) ? $chapterEmails[$chapterName] : [];
            $ccRecipients = isset($coordinatorEmails[$chapterName]) ? $coordinatorEmails[$chapterName] : [];

            if (! empty($toRecipients)) {
                // Split recipients into batches of 50 - so won't be over 100 after adding ccRecipients
                $toBatches = array_chunk($toRecipients, 25);

                foreach ($toBatches as $toBatch) {
                    Mail::to($toBatch)
                        ->cc($ccRecipients)
                        ->queue(new PaymentsReRegLate($data));
                }
            }
        }

        try {
            DB::commit();
        } catch (\Exception $e) {
            // Rollback Transaction
            echo $e->getMessage();
            exit();
            // Log the error
            Log::error($e);

            return redirect()->back()->with('fail', 'Something went wrong, Please try again.');
        }

        return redirect()->to('/chapter/re-registration')->with('success', 'Re-Registration Late Reminders have been successfully sent.');
    }

    /**
     * New Big Sister Welcome Email
     */
    public function createBigSisterEmail(Request $request, $id)
    {
        // Find the coordinator details for the current user
        $corDetails = User::find($request->user()->id)->Coordinators;

        // Get the necessary details from the coordinator details
        $corId = $corDetails['id'];
        $userName = $corDetails['first_name'].' '.$corDetails['last_name'];
        $userEmail = $corDetails['email'];
        $positionId = $corDetails['position_id'];
        $position = CoordinatorPosition::find($positionId);
        $positionTitle = $position['long_title'];
        $lastUpdatedBy = $userName;
        $cordinatorId = $id;

        DB::beginTransaction();
        try {
            $coordinatorDetails = DB::table('coordinators as cd')
                ->select('cd.*', 'cf.conference_description as conf_name', 'rg.long_name as reg_name', 'cd2.first_name as cor_fname', 'cd2.last_name as cor_lname', 'cd2.email as cor_email',
                    'cd2.phone as cor_phone', 'cd2.conference_id as conf', 'cd2.id as pcid')
                ->leftJoin('coordinators as cd2', 'cd.report_id', '=', 'cd2.id')
                ->leftJoin('conference as cf', 'cd.conference_id', '=', 'cf.id')
                ->leftJoin('region as rg', 'cd.region_id', '=', 'rg.id')
                ->where('cd.id', $cordinatorId)
                ->get();

            $chapters = DB::table('chapters as ch')
                ->select('ch.name as chapter', 'st.state_short_name as state')
                ->leftJoin('coordinators as cd', 'cd.id', '=', 'ch.primary_coordinator_id')
                ->leftJoin('state as st', 'ch.state', '=', 'st.id')
                ->where('ch.is_Active', '=', '1')
                ->where('primary_coordinator_id', $cordinatorId)
                ->orderBy('st.state_short_name')
                ->orderBy('ch.name')
                ->get();

            $firstName = $coordinatorDetails[0]->first_name;
            $lastName = $coordinatorDetails[0]->last_name;
            $email = $coordinatorDetails[0]->email;
            $sec_email = $coordinatorDetails[0]->sec_email;
            $cor_fname = $coordinatorDetails[0]->cor_fname;
            $cor_lname = $coordinatorDetails[0]->cor_lname;
            $cor_email = $coordinatorDetails[0]->cor_email;
            $cor_phone = $coordinatorDetails[0]->cor_phone;
            $conf_name = $coordinatorDetails[0]->conf_name;
            $reg_name = $coordinatorDetails[0]->reg_name;
            $conf = $coordinatorDetails[0]->conf;

            // Call the getCCMemail function
            $pcid = $coordinatorDetails[0]->pcid;
            $cc_string = $this->getCCMail($pcid);

            if ($sec_email !== null) {
                $to_email = [$email, $sec_email];
            } else {
                $to_email = [$email];
            }
            $cc_email = $cc_string;

            $mailData = [
                'firstName' => $firstName,
                'lastName' => $lastName,
                'email' => $email,
                'cor_fname' => $cor_fname,
                'cor_lname' => $cor_lname,
                'cor_email' => $cor_email,
                'cor_phone' => $cor_phone,
                'chapters' => $chapters,
                'conf_name' => $conf_name,
                'reg_name' => $reg_name,
                'userName' => $userName,
                'userEmail' => $userEmail,
                'positionTitle' => $positionTitle,
                'conf' => $conf,
            ];

            Mail::to($to_email)
                ->cc($cc_email)
                ->queue(new BigSisterWelcome($mailData));

            DB::commit();

            return redirect()->back()->with('success', 'Welcome letter has been successfully sent');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error sending welcome letter:', ['error' => $e->getMessage()]);

            return redirect()->back()->with('fail', 'Something went wrong, Please try again.');
        }
    }

    /**
     * New Chapter Welcome Email
     */
    public function createNewChapterEmail(Request $request, $id)
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
            ->select('chapters.id as id', 'chapters.name as chapter_name', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cd.email as cor_email',
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
            $firstName = $chapterDetails[0]->pres_fname;
            $lastName = $chapterDetails[0]->pres_lname;
            $email = $chapterDetails[0]->pres_email;
            $cor_fname = $chapterDetails[0]->cor_f_name;
            $cor_lname = $chapterDetails[0]->cor_l_name;
            $cor_email = $chapterDetails[0]->cor_email;

            // Call the getCCMemail function
            $pcid = $chapterDetails[0]->pcid;
            $cc_string = $this->getCCMail($pcid);

            $to_email = [$email];
            $cc_email = $cc_string;

            $mailData = [
                'chapter' => $chapter,
                'state' =>$state,
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

            Mail::to($to_email)
                ->cc($cc_email)
                ->queue(new NewChapterWelcome($mailData));

            DB::commit();

            return redirect()->back()->with('success', 'Welcome letter has been successfully sent');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error sending welcome letter:', ['error' => $e->getMessage()]);

            return redirect()->back()->with('fail', 'Something went wrong, Please try again.');
        }
    }


    /**
     * New Chapter Welcome Letter
     */
    public function createNewChapterLetter(Request $request, $id)
    {

    }




}
