<?php

namespace App\Console\Commands;

use App\Models\Chapters;
use App\Models\DocumentsIRS;
use App\Models\Irs990nFiling;
use App\Services\PositionConditionsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncIrs990n extends Command
{
    protected $signature = 'irs:sync-990n
        {--dry-run : Preview matches without saving}
        {--file= : Path to a local copy of the bulk file instead of downloading}';

    protected $description = 'Cross-reference chapter EINs against the IRS 990-N e-Postcard bulk data and store all matching filing years';

    protected string $bulkDataUrl = 'https://apps.irs.gov/pub/epostcard/data-download-epostcard.zip';

    public function __construct(protected PositionConditionsService $positionConditionsService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $reportYearOptions = $this->positionConditionsService->getReportYearOptions();
        $currentReportYear = $reportYearOptions['reportYearStart'];

        $localPath = $this->option('file');

        // 1. Build a lookup of chapter EINs we actually care about (normalized, digits only)
        $chapters = Chapters::whereNotNull('ein')->where('ein', '!=', '')->get();

        if ($chapters->isEmpty()) {
            $this->warn('No chapters with an EIN on file. Nothing to do.');
            return self::SUCCESS;
        }

        $chapterByEin = [];
        foreach ($chapters as $chapter) {
            $normalized = preg_replace('/\D/', '', $chapter->ein);
            if (strlen($normalized) === 9) {
                $chapterByEin[$normalized] = $chapter;
            }
        }

        $this->info('Tracking '.count($chapterByEin).' chapter EINs.');

        // 2. Get the bulk file onto local disk (stream download, don't hold in memory)
        if ($localPath) {
            $filePath = $localPath;
        } else {
            $zipPath = storage_path('app/irs-990n-bulk.zip');
            $this->info('Downloading IRS bulk file...');

            if (!$this->downloadToDisk($this->bulkDataUrl, $zipPath)) {
                $this->error('Failed to download IRS bulk data.');
                Log::error('IRS 990-N sync: download failed', ['url' => $this->bulkDataUrl]);
                return self::FAILURE;
            }

            $this->info('Extracting bulk file...');
            $zip = new \ZipArchive();
            if ($zip->open($zipPath) !== true) {
                $this->error('Failed to open downloaded zip file.');
                Log::error('IRS 990-N sync: zip open failed', ['path' => $zipPath]);
                @unlink($zipPath);
                return self::FAILURE;
            }

            $entryName = $zip->getNameIndex(0);
            $zip->extractTo(storage_path('app'), [$entryName]);
            $zip->close();
            @unlink($zipPath); // cleanup the zip, we only need the extracted file

            $filePath = storage_path('app/'.$entryName);
        }

        // 3. Stream-parse line by line, pipe-delimited, no header row.
        // Columns (0-indexed), based on observed sample data:
        // 0 EIN | 1 Tax Year | 2 Org Name | 3 ? | 4 ? | 5 Period Begin | 6 Period End | 7 Website/Email | 8 Officer Name ...
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            $this->error('Could not open bulk file for reading.');
            return self::FAILURE;
        }

        $matchesByEin = []; // ein => [ [tax_year, period_begin, period_end, org_name], ... ]
        $lineCount = 0;

        while (($line = fgetcsv($handle, 0, '|')) !== false) {
            $lineCount++;

            $ein = $line[0] ?? null;
            if (!$ein || !isset($chapterByEin[$ein])) {
                continue; // not one of ours, skip immediately
            }

            $matchesByEin[$ein][] = [
                'tax_year' => $line[1] ?? null,
                'org_name' => $line[2] ?? null,
                'period_begin' => $this->parseDate($line[5] ?? null),
                'period_end' => $this->parseDate($line[6] ?? null),
            ];
        }

        fclose($handle);

        if (!$localPath) {
            @unlink($filePath); // cleanup the downloaded copy
        }

        $this->info("Scanned {$lineCount} rows. Found filings for ".count($matchesByEin).' of your tracked EINs.');

        // 4. Store every filing year found — updateOrCreate keyed on chapter + tax year
        $filingsWritten = 0;
        $chaptersWithNoFilings = 0;

        foreach ($chapterByEin as $ein => $chapter) {
            if (!isset($matchesByEin[$ein])) {
                $chaptersWithNoFilings++;
                continue;
            }

            $documentsIrs = DocumentsIRS::where('chapter_id', $chapter->id)->first();
            $wasAlreadyVerified = $documentsIrs?->irs_verified;

            $mostRecentYear = collect($matchesByEin[$ein])->max('tax_year');

            foreach ($matchesByEin[$ein] as $filing) {
                if ($this->option('dry-run')) {
                    $this->line("Would record: {$chapter->name} (EIN: {$ein}) — Tax Year {$filing['tax_year']}");
                    continue;
                }

                Irs990nFiling::updateOrCreate(
                    ['chapter_id' => $chapter->id, 'tax_year' => $filing['tax_year']],
                    [
                        'ein' => $ein,
                        'tax_period_begin' => $filing['period_begin'],
                        'tax_period_end' => $filing['period_end'],
                        'organization_name' => $filing['org_name'],
                        'synced_at' => now(),
                    ]
                );

                $filingsWritten++;
            }

            if (!$this->option('dry-run') && (string) $mostRecentYear === (string) $currentReportYear) {
                $documentsIrs?->update(['irs_verified' => 1]);
            }

            if (!$this->option('dry-run') && $wasAlreadyVerified) {
                $this->flagIfVerifiedButDatesWrong($documentsIrs, $matchesByEin[$ein], $currentReportYear);
            }
        }

        $this->info("Filing records written: {$filingsWritten} | Chapters with no filings found: {$chaptersWithNoFilings}");
        Log::info('IRS 990-N sync completed', [
            'filings_written' => $filingsWritten,
            'chapters_with_no_filings' => $chaptersWithNoFilings,
            'total_chapters_tracked' => count($chapterByEin),
        ]);

        return self::SUCCESS;
    }

    protected function parseDate(?string $value): ?string
    {
        if (!$value) return null;

        // Expected format in file: MM-DD-YYYY
        $parts = explode('-', $value);
        if (count($parts) !== 3) return null;

        [$month, $day, $year] = $parts;
        return "{$year}-{$month}-{$day}"; // convert to Y-m-d for the date column
    }

    protected function downloadToDisk(string $url, string $destination): bool
    {
        $fp = fopen($destination, 'w');
        if (!$fp) return false;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        $success = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        fclose($fp);

        return $success && $httpCode === 200;
    }

    protected function flagIfVerifiedButDatesWrong(DocumentsIRS $documentsIrs, array $filings, string $currentReportYear): void
    {
        $currentYearFiling = collect($filings)->firstWhere('tax_year', $currentReportYear);

        if (!$currentYearFiling) {
            return; // no filing for the current report year to check against
        }

        $expectedPeriodBegin = "{$currentReportYear}-07-01";

        if ($currentYearFiling['period_begin'] !== $expectedPeriodBegin) {
            $documentsIrs->update([
                'irs_issues' => 1,
                'irs_filedwrong' => 1,
            ]);
        }
    }
}
