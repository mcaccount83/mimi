<?php

namespace App\Console\Commands;

use App\Models\Chapters;
use App\Models\Irs990nFiling;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncIrs990n extends Command
{
    protected $signature = 'irs:sync-990n
        {--dry-run : Preview matches without saving}
        {--file= : Path to a local copy of the bulk file instead of downloading}';

    protected $description = 'Cross-reference chapter EINs against the IRS 990-N e-Postcard bulk data and store all matching filing years';

    // TODO: confirm current bulk download URL on irs.gov before enabling in production
    protected string $bulkDataUrl = 'https://www.irs.gov/pub/irs-soi/eo_epostcard.txt'; // PLACEHOLDER — verify

    public function handle(): int
    {
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
            $filePath = storage_path('app/irs-990n-bulk.txt');
            $this->info('Downloading IRS bulk file...');

            if (!$this->downloadToDisk($this->bulkDataUrl, $filePath)) {
                $this->error('Failed to download IRS bulk data.');
                Log::error('IRS 990-N sync: download failed');
                return self::FAILURE;
            }
        }

        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return self::FAILURE;
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

            foreach ($matchesByEin[$ein] as $filing) {
                if ($this->option('dry-run')) {
                    $this->line("Would record: {$chapter->name} (EIN: {$ein}) — Tax Year {$filing['tax_year']}");
                    continue;
                }

                Irs990nFiling::updateOrCreate(
                    [
                        'chapter_id' => $chapter->id,
                        'tax_year' => $filing['tax_year'],
                    ],
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
}
