<?php

namespace App\Console\Commands;

use App\Http\Controllers\GoogleController;
use App\Models\GoogleDrive;
use App\Services\ExportService;
use App\Services\PositionConditionsService;
use Illuminate\Console\Command;

class ExportInternationalReportsToDrive extends Command
{
    protected $signature = 'exports:international-to-drive';
    protected $description = 'Generates the international chapter/coordinator CSV exports and uploads them to a date-stamped Google Drive subfolder';

    public function handle(
        ExportService $exportService,
        GoogleController $googleController,
        PositionConditionsService $positionConditionsService
    ) {
        $googleDrive = GoogleDrive::where('name', 'backup_files')->first();

        if (! $googleDrive) {
            $this->error("No GoogleDrive record found for 'backup_files' — create one with the target folder_id first.");
            return self::FAILURE;
        }

        $dateOptions = $positionConditionsService->getDateOptions();
        $dateFolderName = $dateOptions['currentDateYmd'];

        $dateFolderId = $googleController->getOrCreateDateFolder($dateFolderName, $googleDrive->folder_id);

        $user = [
            'cdId' => null, 'confId' => null, 'regId' => null,
            'cdPositionId' => null, 'cdSecPositionId' => null,
        ];

        $exports = [
            'International chapters' => $exportService->generateChapters($user, international: true),
            'International zapped chapters' => $exportService->generateZappedChapters($user, international: true),
            'International coordinators' => $exportService->generateCoordinators($user, international: true),
            'International retired coordinators' => $exportService->generateRetiredCoordinators($user, international: true),
        ];

        foreach ($exports as $label => $path) {
            if (! $path) {
                $this->warn("{$label}: no records — skipped.");
                continue;
            }

            $filecontent = file_get_contents($path);
            $fileId = $googleController->uploadToGoogleDrive(
                basename($path), 'text/csv', $filecontent, $dateFolderId
            );

            @unlink($path);

            if ($fileId) {
                $this->info("{$label}: uploaded ".basename($path)." to folder {$dateFolderName} (Drive file ID {$fileId})");
            } else {
                $this->error("{$label}: upload failed for ".basename($path));
            }
        }

        return self::SUCCESS;
    }
}
