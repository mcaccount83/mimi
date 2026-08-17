<?php

namespace App\Services;

use App\Enums\CheckboxFilterEnum;
use App\Http\Controllers\BaseChapterController;
use App\Http\Controllers\BaseCoordinatorController;

class ExportService
{
    public function __construct(
        protected BaseChapterController $baseChapterController,
        protected BaseCoordinatorController $baseCoordinatorController,
        protected PositionConditionsService $positionConditionsService,
    ) {}

    /*
    |--------------------------------------------------------------------
    | Public export entry points (international + domestic share these)
    |--------------------------------------------------------------------
    */

    public function generateChapters(array $user, bool $international = false): ?string
    {
        $chapterIds = $this->resolveChapterIds(1, $international, $user);

        if (empty($chapterIds)) {
            return null;
        }

        return $this->writeCsv(
            ids: $chapterIds,
            headerRow: [
                'EIN', 'EIN Letter', 'Conference', 'Region', 'State', 'Name', 'Bounraries', 'Status',
                'Notes', 'Primary Coordinator', 'Inquiries Email', 'Inquiries Notes', 'Chapter Email',
                'Chapter P.O. Box', 'President Name', 'President Email', 'President Phone',
                'AVP Name', 'AVP Email', 'AVP Phone', 'MVP Name', 'MVP Email', 'MVP Phone',
                'Treasurer Name', 'Treasurer Email', 'Treasurer Phone', 'Secretary Name',
                'Secretary Email', 'Secretary Phone', 'Website', 'Linked Status', 'EGroup',
                'Social Media', 'Next Renewal', 'Dues Last Paid', 'Members paid for', 'Re-Reg Notes',
                'Start Month', 'Start Year', 'Founder', 'Sistered By', 'FormerName',
            ],
            fetchers: [
                fn (array $chunk) => $this->baseChapterController->getChapterDetailsForExport($chunk),
                fn (array $chunk) => $this->baseChapterController->getActiveBoardDetailsForExport($chunk),
            ],
            rowFormatter: fn (array $row) => $this->formatFullChapterRow($row),
            fileNameSuffix: $international ? 'int_chapter_export' : 'chapter_export',
        );
    }

    public function generateZappedChapters(array $user, bool $international = false): ?string
    {
        $chapterIds = $this->resolveChapterIds(0, $international, $user);

        if (empty($chapterIds)) {
            return null;
        }

        return $this->writeCsv(
            ids: $chapterIds,
            headerRow: [
                'EIN', 'EIN Letter', 'Conference', 'Region', 'State', 'Name', 'Bounraries', 'Status',
                'Notes', 'Primary Coordinator', 'Inquiries Email', 'Inquiries Notes', 'Chapter Email',
                'Chapter P.O. Box', 'President Name', 'President Email', 'President Phone',
                'AVP Name', 'AVP Email', 'AVP Phone', 'MVP Name', 'MVP Email', 'MVP Phone',
                'Treasurer Name', 'Treasurer Email', 'Treasurer Phone', 'Secretary Name',
                'Secretary Email', 'Secretary Phone', 'Website', 'Linked Status', 'EGroup',
                'Social Media', 'Next Renewal', 'Dues Last Paid', 'Members paid for', 'Re-Reg Notes',
                'Start Month', 'Start Year', 'Founder', 'Sistered By', 'FormerName',
                'Disband Date', 'Disband Reason',
            ],
            fetchers: [
                fn (array $chunk) => $this->baseChapterController->getChapterDetailsForExport($chunk),
                fn (array $chunk) => $this->baseChapterController->getDisbandedBoardDetailsForExport($chunk),
            ],
            rowFormatter: fn (array $row) => $this->formatZappedChapterRow($row),
            fileNameSuffix: $international ? 'int_chapter_zap_export' : 'chapter_zap_export',
        );
    }

    public function generateReRegList(array $user, bool $international = false): ?string
    {
        $chapterIds = $this->resolveOverdueReRegChapterIds($international, $user);

        if (empty($chapterIds)) {
            return null;
        }

        return $this->writeCsv(
            ids: $chapterIds,
            headerRow: [
                'Conference', 'Region', 'State', 'Name',
                'Start Month', 'Start Year', 'Next Renewal Year', 'Dues Last Paid',
                'Members paid for', 'Re-Reg Notes',
            ],
            fetchers: [
                fn (array $chunk) => $this->baseChapterController->getChapterDetailsForExport($chunk),
            ],
            rowFormatter: fn (array $row) => $this->formatReRegRow($row),
            fileNameSuffix: $international ? 'int_rereg_export' : 'rereg_export',
        );
    }

    public function generateEINStatusList(array $user, bool $international = false): ?string
    {
        $chapterIds = $this->resolveChapterIds(1, $international, $user);

        if (empty($chapterIds)) {
            return null;
        }

        return $this->writeCsv(
            ids: $chapterIds,
            headerRow: [
                'Conference', 'Region', 'State', 'Name', 'EIN', 'EIN Letter', 'Start Month', 'Start Year',
                'Pres Name', 'Pres Address', 'Pres City', 'Pres State', 'Pres Zip', 'Pres Phone', 'Pres Email',
            ],
            fetchers: [
                fn (array $chunk) => $this->baseChapterController->getChapterDetailsForExport($chunk),
                fn (array $chunk) => $this->baseChapterController->getActiveBoardDetailsForExport($chunk),
            ],
            rowFormatter: fn (array $row) => $this->formatEINStatusRow($row),
            fileNameSuffix: $international ? 'int_ein_status_export' : 'ein_status_export',
        );
    }

    public function generateEOYStatusList(array $user, bool $international = false): ?string
    {
        $chapterIds = $this->resolveChapterIds(1, $international, $user);

        if (empty($chapterIds)) {
            return null;
        }

        return $this->writeCsv(
            ids: $chapterIds,
            headerRow: [
                'Conference', 'Region', 'State', 'Name', 'Primary Coordinator', 'Board Report Received', 'Board Report Activated', 'Financial Report Received',
                'Financial Review Complete', 'Report Notes', 'Extension Given', 'Extension Notes',
            ],
            fetchers: [
                fn (array $chunk) => $this->baseChapterController->getChapterDetailsForExport($chunk),
            ],
            rowFormatter: fn (array $row) => $this->formatEOYStatusRow($row),
            fileNameSuffix: $international ? 'int_eoy_status_export' : 'eoy_status_export',
        );
    }

    public function generateCoordinators(array $user, bool $international = false): ?string
    {
        $coordIds = $this->resolveCoordinatorIds(1, $international, $user);

        if (empty($coordIds)) {
            return null;
        }

        return $this->writeCsv(
            ids: $coordIds,
            headerRow: [
                'Conference', 'Region', 'Coordinator Name', 'Position', 'Sec Position', 'Admin',
                'Report To', 'Email', 'Email2', 'Phone', 'Phone2', 'Address', 'City', 'State', 'Zip',
                'Birthday', 'Coordinator Start', 'Last Promoted', 'Leave of Absense', 'Leave Date',
            ],
            fetchers: [
                fn (array $chunk) => $this->baseCoordinatorController->getCoordinatorDetailsForExport($chunk),
            ],
            rowFormatter: fn (array $row) => $this->formatFullCoordinatorRow($row),
            fileNameSuffix: $international ? 'int_coord_export' : 'coord_export',
        );
    }

    public function generateRetiredCoordinators(array $user, bool $international = false): ?string
    {
        $coordIds = $this->resolveCoordinatorIds(0, $international, $user);

        if (empty($coordIds)) {
            return null;
        }

        return $this->writeCsv(
            ids: $coordIds,
            headerRow: [
                'Conference', 'Region', 'Coordinator Name', 'Position', 'Sec Position', 'Admin',
                'Report To', 'Email', 'Email2', 'Phone', 'Phone2', 'Address', 'City', 'State', 'Zip',
                'Birthday', 'Coordinator Start', 'Last Promoted', 'Leave of Absense', 'Leave Date',
                'Retire Date', 'Retire Reason',
            ],
            fetchers: [
                fn (array $chunk) => $this->baseCoordinatorController->getCoordinatorDetailsForExport($chunk),
            ],
            rowFormatter: fn (array $row) => $this->formatRetiredCoordinatorRow($row),
            fileNameSuffix: $international ? 'int_coord_retired_export' : 'coord_retired_export',
        );
    }

    public function generateAppreciationList(array $user, bool $international = false): ?string
    {
        $coordIds = $this->resolveCoordinatorIds(1, $international, $user);

        if (empty($coordIds)) {
            return null;
        }

        return $this->writeCsv(
            ids: $coordIds,
            headerRow: [
                'Conference', 'Region', 'Coordinator Name', 'Position', 'Sec Position',
                'Start Date', '<1 Year', '1 Year', '2 Year', '3 Year', '4 Year', '5 Year', '6 Year',
                '7 Year', '8 Year', '9 Year', 'Necklace', 'Top Tier/Other',
            ],
            fetchers: [
                fn (array $chunk) => $this->baseCoordinatorController->getCoordinatorDetailsForExport($chunk),
            ],
            rowFormatter: fn (array $row) => $this->formatCoordinatorAppreciationRow($row),
            fileNameSuffix: $international ? 'int_coordinator_appreciation_export' : 'coordinator_appreciation_export',
        );
    }

    /*
    |--------------------------------------------------------------------
    | ID resolution
    |--------------------------------------------------------------------
    */

    protected function resolveChapterIds(int $activeFlag, bool $international, array $user): array
    {
        if ($international) {
            $_GET[CheckboxFilterEnum::INTERNATIONAL] = 'yes';
        }

        $baseQuery = $this->baseChapterController->getBaseQuery(
            $activeFlag, $user['cdId'], $user['confId'], $user['regId'],
            $user['cdPositionId'], $user['cdSecPositionId']
        );
        $ids = $baseQuery['query']->pluck('id')->toArray();

        unset($_GET[CheckboxFilterEnum::INTERNATIONAL]);

        return $ids;
    }

    protected function resolveOverdueReRegChapterIds(bool $international, array $user): array
    {
        if ($international) {
            $_GET[CheckboxFilterEnum::INTERNATIONAL] = 'yes';
        }

        $baseQuery = $this->baseChapterController->getBaseQuery(
            1, $user['cdId'], $user['confId'], $user['regId'],
            $user['cdPositionId'], $user['cdSecPositionId']
        );

        $dateOptions = $this->positionConditionsService->getDateOptions();
        $currentYear = $dateOptions['currentYear'];
        $lastMonth = $dateOptions['lastMonth'];

        $ids = $baseQuery['query']
            ->where(function ($query) use ($currentYear, $lastMonth) {
                $query->where('next_renewal_year', '<', $currentYear)
                    ->orWhere(function ($query) use ($currentYear, $lastMonth) {
                        $query->where('next_renewal_year', '=', $currentYear)
                            ->where('start_month_id', '<=', $lastMonth);
                    });
            })
            ->orderByDesc('start_month_id')
            ->orderByDesc('next_renewal_year')
            ->pluck('id')
            ->toArray();

        unset($_GET[CheckboxFilterEnum::INTERNATIONAL]);

        return $ids;
    }

    protected function resolveCoordinatorIds(int $activeFlag, bool $international, array $user): array
    {
        if ($international) {
            $_GET[CheckboxFilterEnum::INTERNATIONAL] = 'yes';
        }

        $baseQuery = $this->baseCoordinatorController->getBaseQuery(
            $activeFlag, $user['cdId'], $user['confId'], $user['regId'],
            $user['cdPositionId'], $user['cdSecPositionId']
        );
        $ids = $baseQuery['query']->pluck('id')->toArray();

        unset($_GET[CheckboxFilterEnum::INTERNATIONAL]);

        return $ids;
    }

    /*
    |--------------------------------------------------------------------
    | Shared CSV writer
    |--------------------------------------------------------------------
    */

    protected function writeCsv(array $ids, array $headerRow, array $fetchers, callable $rowFormatter, string $fileNameSuffix): string
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $dateOptions = $this->positionConditionsService->getDateOptions();
        $fileName = $dateOptions['currentDateYmd']."_{$fileNameSuffix}.csv";
        $path = storage_path('app/exports/'.$fileName);

        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $file = fopen($path, 'w');
        fputcsv($file, $headerRow);

        foreach (array_chunk($ids, 200) as $chunk) {
            $caches = array_map(fn ($fetch) => $fetch($chunk), $fetchers);

            foreach ($chunk as $id) {
                if (! isset($caches[0][$id])) {
                    continue;
                }

                $combinedData = $caches[0][$id];
                for ($i = 1; $i < count($caches); $i++) {
                    $combinedData = array_merge($combinedData, $caches[$i][$id] ?? []);
                }

                fputcsv($file, $rowFormatter($combinedData));
            }

            unset($caches);
            gc_collect_cycles();
        }

        fclose($file);

        return $path;
    }

    /*
    |--------------------------------------------------------------------
    | Chapter field-group formatters (moved verbatim from ExportController)
    |--------------------------------------------------------------------
    */

    public function formatChapterEINInfo(array $chapterData)
    {
        $chDetails = $chapterData['chDetails'];
        $chDocuments = $chapterData['chDocuments'] ?? null;

        return [
            'EIN' => $chDetails->ein,
            'EIN Letter' => ($chDocuments && $chDocuments->ein_letter == 1) ? 'YES' : 'NO',
        ];
    }

    public function formatChapterLocationInfo(array $chapterData)
    {
        return [
            'Conference' => $chapterData['chConfId'],
            'Region' => $chapterData['regionLongName'],
            'State' => $chapterData['stateShortName'],
        ];
    }

    public function formatChapterNameInfo(array $chapterData)
    {
        return [
            'Name' => $chapterData['chDetails']->name,
        ];
    }

    public function formatChapterStatusInfo(array $chapterData)
    {
        $chDetails = $chapterData['chDetails'];

        return [
            'Bounraries' => $chDetails->territory,
            'Status' => $chapterData['chapterStatus'] ?? '',
            'Notes' => $chDetails->notes,
        ];
    }

    public function formatChapterPCInfo(array $chapterData)
    {
        return [
            'Primary Coordinator' => $chapterData['pcName'] ?? '',
        ];
    }

    public function formatChapterContactInfo(array $chapterData)
    {
        $chDetails = $chapterData['chDetails'];

        return [
            'Inquiries Email' => $chDetails->inquiries_contact,
            'Inquiries Notes' => $chDetails->inquiries_note,
            'Chapter Email' => $chDetails->email,
            'Chapter P.O. Box' => $chDetails->po_box,
        ];
    }

    public function formatWebsiteInfo(array $chapterData)
    {
        $chDetails = $chapterData['chDetails'];

        return [
            'Website' => $chapterData['websiteLink'] ?? '',
            'Linked Status' => $chDetails->website_status,
            'EGroup' => $chDetails->egroup,
            'Social Media' => trim(($chDetails->social1 ?? '').' '.($chDetails->social2 ?? '').' '.($chDetails->social3 ?? '')),
        ];
    }

    public function formatPaymentInfo(array $chapterData)
    {
        $chDetails = $chapterData['chDetails'];
        $chPayments = $chapterData['chPayments'] ?? null;

        return [
            'Next Renewal' => $chDetails->next_renewal_year,
            'Dues Last Paid' => $chPayments->rereg_date,
            'Members paid for' => $chPayments->rereg_members,
            'Re-Reg Notes' => $chPayments->rereg_notes,
        ];
    }

    public function formatChapterStartInfo(array $chapterData)
    {
        return [
            'Start Month' => $chapterData['startMonthName'] ?? '',
            'Start Year' => $chapterData['chDetails']->start_year,
        ];
    }

    public function formatChapterHistoryInfo(array $chapterData)
    {
        $chDetails = $chapterData['chDetails'];

        return [
            'Founder' => $chDetails->founders_name,
            'Sistered By' => $chDetails->sistered_by,
            'FormerName' => $chDetails->former_name,
        ];
    }

    public function formatEOYInfo(array $chapterData)
    {
        $chEOYDocuments = $chapterData['chEOYDocuments'];

        return [
            'Board Report Received' => ($chEOYDocuments->new_board_submitted == 1) ? 'YES' : 'NO',
            'Board Report Activated' => ($chEOYDocuments->new_board_active == 1) ? 'YES' : 'NO',
            'Financial Report Received' => ($chEOYDocuments->financial_report_received == 1) ? 'YES' : 'NO',
            'Financial Review Complete' => ($chEOYDocuments->financial_review_complete == 1) ? 'YES' : 'NO',
            'Report Notes' => $chEOYDocuments->report_notes,
            'Extension Given' => ($chEOYDocuments->report_extension == 1) ? 'YES' : 'NO',
            'Extension Notes' => $chEOYDocuments->extension_notes,
        ];
    }

    public function formatPresidentInfo(array $chapterData)
    {
        $PresDetails = $chapterData['PresDetails'];

        return [
            'Pres Name' => ($PresDetails && $PresDetails->first_name) ? $PresDetails->first_name.' '.$PresDetails->last_name : '',
            'Pres Address' => $PresDetails->street_address ?? '',
            'Pres City' => $PresDetails->city ?? '',
            'Pres State' => $PresDetails->state->state_short_name ?? '',
            'Pres Zip' => $PresDetails->zip ?? '',
            'Pres Phone' => $PresDetails->phone ?? '',
            'Pres Email' => $PresDetails->email ?? '',
        ];
    }

    public function formatDisbandedInfo(array $chapterData)
    {
        $chDetails = $chapterData['chDetails'];

        return [
            'Disband Date' => $chDetails->zap_date,
            'Disband Reason' => $chDetails->disband_reason,
        ];
    }

    public function formatBoardMemberInfo(array $chapterData)
    {
        return $this->mapBoardRoles($chapterData, [
            'President' => 'PresDetails', 'AVP' => 'AVPDetails', 'MVP' => 'MVPDetails',
            'Treasurer' => 'TRSDetails', 'Secretary' => 'SECDetails',
        ]);
    }

    public function formatDisbandedBoardMemberInfo(array $chapterData)
    {
        // Same shape as active board — kept as a separate method to match the
        // controller's existing naming/call sites for the zapped export.
        return $this->formatBoardMemberInfo($chapterData);
    }

    protected function mapBoardRoles(array $chapterData, array $roles): array
    {
        $row = [];

        foreach ($roles as $label => $key) {
            $details = $chapterData[$key] ?? null;
            $row["{$label} Name"] = ($details && $details->first_name) ? $details->first_name.' '.$details->last_name : '';
            $row["{$label} Email"] = $details->email ?? '';
            $row["{$label} Phone"] = $details->phone ?? '';
        }

        return $row;
    }

    public function formatFullChapterRow(array $chapterData)
    {
        return array_merge(
            $this->formatChapterEINInfo($chapterData),
            $this->formatChapterLocationInfo($chapterData),
            $this->formatChapterNameInfo($chapterData),
            $this->formatChapterStatusInfo($chapterData),
            $this->formatChapterPCInfo($chapterData),
            $this->formatChapterContactInfo($chapterData),
            $this->formatBoardMemberInfo($chapterData),
            $this->formatWebsiteInfo($chapterData),
            $this->formatPaymentInfo($chapterData),
            $this->formatChapterStartInfo($chapterData),
            $this->formatChapterHistoryInfo($chapterData)
        );
    }

    public function formatZappedChapterRow(array $chapterData)
    {
        return array_merge(
            $this->formatChapterEINInfo($chapterData),
            $this->formatChapterLocationInfo($chapterData),
            $this->formatChapterNameInfo($chapterData),
            $this->formatChapterStatusInfo($chapterData),
            $this->formatChapterPCInfo($chapterData),
            $this->formatChapterContactInfo($chapterData),
            $this->formatDisbandedBoardMemberInfo($chapterData),
            $this->formatWebsiteInfo($chapterData),
            $this->formatPaymentInfo($chapterData),
            $this->formatChapterStartInfo($chapterData),
            $this->formatChapterHistoryInfo($chapterData),
            $this->formatDisbandedInfo($chapterData)
        );
    }

    public function formatReRegRow(array $chapterData)
    {
        return array_merge(
            $this->formatChapterLocationInfo($chapterData),
            $this->formatChapterNameInfo($chapterData),
            $this->formatChapterStartInfo($chapterData),
            $this->formatPaymentInfo($chapterData)
        );
    }

    public function formatEINStatusRow(array $chapterData)
    {
        return array_merge(
            $this->formatChapterLocationInfo($chapterData),
            $this->formatChapterNameInfo($chapterData),
            $this->formatChapterEINInfo($chapterData),
            $this->formatChapterStartInfo($chapterData),
            $this->formatPresidentInfo($chapterData)
        );
    }

    public function formatEOYStatusRow(array $chapterData)
    {
        return array_merge(
            $this->formatChapterLocationInfo($chapterData),
            $this->formatChapterNameInfo($chapterData),
            $this->formatChapterPCInfo($chapterData),
            $this->formatEOYInfo($chapterData)
        );
    }

    /*
    |--------------------------------------------------------------------
    | Coordinator field-group formatters (moved verbatim)
    |--------------------------------------------------------------------
    */

    public function formatCoordinatorLocationInfo(array $coordData)
    {
        $cdDetails = $coordData['cdDetails'];

        return [
            'Conference' => $coordData['cdConfId'],
            'Region' => $coordData['regionLongName'],
            'Coordinator Name' => $cdDetails->first_name.' '.$cdDetails->last_name,
        ];
    }

    public function formatCoordinatorPositionInfo(array $coordData)
    {
        $displayPosition = $coordData['displayPosition'];
        $secondaryPosition = $coordData['secondaryPosition'];
        $secPositionValue = null;

        if ($secondaryPosition) {
            if (is_object($secondaryPosition)) {
                $secPositionValue = $secondaryPosition->long_title ?? null;
            } elseif (is_array($secondaryPosition) || $secondaryPosition instanceof \Traversable) {
                $titles = [];
                foreach ($secondaryPosition as $position) {
                    if (is_object($position)) {
                        $titles[] = $position->long_title ?? '';
                    } elseif (is_string($position)) {
                        $titles[] = $position;
                    }
                }
                $secPositionValue = ! empty($titles) ? implode(', ', $titles) : null;
            } elseif (is_string($secondaryPosition)) {
                $secPositionValue = $secondaryPosition;
            }
        }

        return [
            'Position' => $displayPosition->long_title,
            'Sec Position' => $secPositionValue,
        ];
    }

    public function formatCoordinatorPositionExtraInfo(array $coordData)
    {
        return [
            'Admin' => $coordData['cdAdminRole']->admin_role,
            'Report To' => $coordData['RptFName'].' '.$coordData['RptLName'],
        ];
    }

    public function formatCoordinatorContactInfo(array $coordData)
    {
        $cdDetails = $coordData['cdDetails'];

        return [
            'Email' => $cdDetails->email,
            'Email2' => $cdDetails->sec_email,
            'Phone' => $cdDetails->phone,
            'Phone2' => $cdDetails->alt_phone,
        ];
    }

    public function formatCoordinatorAddressInfo(array $coordData)
    {
        $cdDetails = $coordData['cdDetails'];

        return [
            'Address' => $cdDetails->address,
            'City' => $cdDetails->city,
            'State' => $cdDetails->state->state_short_name,
            'Zip' => $cdDetails->zip,
        ];
    }

    public function formatCoordinatorBirthdayInfo(array $coordData)
    {
        $cdDetails = $coordData['cdDetails'];

        return [
            'Birthday' => $cdDetails->birthday_month_id.' / '.$cdDetails->birthday_day,
        ];
    }

    public function formatCoordinatorStartInfo(array $coordData)
    {
        return [
            'Coordinator Start' => $coordData['cdDetails']->coordinator_start_date,
        ];
    }

    public function formatCoordinatorHistoryInfo(array $coordData)
    {
        $cdDetails = $coordData['cdDetails'];

        return [
            'Last Promoted' => $cdDetails->last_promoted,
            'Leave of Absense' => ($cdDetails->on_leave == 1) ? 'YES' : 'NO',
            'Leave Date' => $cdDetails->leave_date,
        ];
    }

    public function formatCoordinatorAppreciationInfo(array $coordData)
    {
        $cdDetails = $coordData['cdDetails'];
        $necklace = $cdDetails->recognition->recognition_necklace;

        return [
            '<1 Year' => $cdDetails->recognition->recognitionGift0?->recognition_gift,
            '1 Year' => $cdDetails->recognition->recognitionGift1?->recognition_gift,
            '2 Years' => $cdDetails->recognition->recognitionGift2?->recognition_gift,
            '3 Years' => $cdDetails->recognition->recognitionGift3?->recognition_gift,
            '4 Years' => $cdDetails->recognition->recognitionGift4?->recognition_gift,
            '5 Years' => $cdDetails->recognition->recognitionGift5?->recognition_gift,
            '6 Years' => $cdDetails->recognition->recognitionGift6?->recognition_gift,
            '7 Years' => $cdDetails->recognition->recognitionGift7?->recognition_gift,
            '8 Years' => $cdDetails->recognition->recognitionGift8?->recognition_gift,
            '9 Years' => $cdDetails->recognition->recognitionGift9?->recognition_gift,
            'Necklace' => ($necklace == 1) ? 'YES' : 'NO',
            'Top Tier/Other' => $cdDetails->recognition->recognition_toptier,
        ];
    }

    public function formatCoordinatorRetireInfo(array $coordData)
    {
        $cdDetails = $coordData['cdDetails'];

        return [
            'Retire Date' => $cdDetails->zapped_date,
            'Retire Reason' => $cdDetails->reason_retired,
        ];
    }

    public function formatFullCoordinatorRow(array $coordData)
    {
        return array_merge(
            $this->formatCoordinatorLocationInfo($coordData),
            $this->formatCoordinatorPositionInfo($coordData),
            $this->formatCoordinatorPositionExtraInfo($coordData),
            $this->formatCoordinatorContactInfo($coordData),
            $this->formatCoordinatorAddressInfo($coordData),
            $this->formatCoordinatorBirthdayInfo($coordData),
            $this->formatCoordinatorStartInfo($coordData),
            $this->formatCoordinatorHistoryInfo($coordData)
        );
    }

    public function formatRetiredCoordinatorRow(array $coordData)
    {
        return array_merge(
            $this->formatCoordinatorLocationInfo($coordData),
            $this->formatCoordinatorPositionInfo($coordData),
            $this->formatCoordinatorPositionExtraInfo($coordData),
            $this->formatCoordinatorContactInfo($coordData),
            $this->formatCoordinatorAddressInfo($coordData),
            $this->formatCoordinatorBirthdayInfo($coordData),
            $this->formatCoordinatorHistoryInfo($coordData),
            $this->formatCoordinatorStartInfo($coordData),
            $this->formatCoordinatorRetireInfo($coordData)
        );
    }

    public function formatCoordinatorAppreciationRow(array $coordData)
    {
        return array_merge(
            $this->formatCoordinatorLocationInfo($coordData),
            $this->formatCoordinatorPositionInfo($coordData),
            $this->formatCoordinatorStartInfo($coordData),
            $this->formatCoordinatorAppreciationInfo($coordData)
        );
    }
}
