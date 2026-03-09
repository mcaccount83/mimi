@extends('layouts.mimi_theme')

@section('page_title', 'MOMS Club of ' . $chDetails->name . ', ' . $stateShortName)
@section('breadcrumb', 'EOY Information')

@section('content')
     <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
        <div class="col-12">

            <div class="col-md-12">
            <div class="card card-primary card-outline">
                    <div class="card-body">
                        <div class="card-header bg-transparent border-0">
                             <h3>{{ $fiscalYearEOY }} End of Year Information
                            @if ($ITCondition && !$displayEOYTESTING && !$displayEOYLIVE) *ADMIN*@endif
                            @if ($eoyTestCondition && $displayEOYTESTING) *TESTING*@endif
                            </h3>
                        </div>
                    <!-- /.card-header -->
                    <div class="card-body">

                         <div class="row">
                            <div class="col-md-12 text-center mb-3">
                                @if ($displayEOYTESTING == '1' || $displayEOYLIVE == '1' || $ITCondition)
                                    @if($chEOYDocuments->new_board_active != '1')
                                        <button type="button" class="btn btn-primary bg-gradient" onclick="window.location.href='{{ route('board-new.editboardreport', ['id' => $chDetails->id]) }}'">{{$boardReportName}}</button>
                                    @else
                                        <button type="button" class="btn btn-primary bg-gradient disabled" disabled>{{$boardReportName}} Activated</button>
                                    @endif

                                    <button type="button" class="btn btn-primary bg-gradient" onclick="window.location.href='{{ route('board-new.editfinancialreport', ['id' => $chDetails->id]) }}'">{{$financialReportName}}</button>
                                    @if (!empty($chEOYDocuments->$yearColumnName))
                                        <button type="button" class="btn btn-primary bg-gradient" onclick="openPdfViewer('{{ $chEOYDocuments->$yearColumnName }}')">{{$financialPDFName}}</button>
                                    @endif
                                    <br><br>
                                    @php
                                        $chapter_awards = null;

                                        if (isset($chFinancialReport['chapter_awards']) && !empty($chFinancialReport['chapter_awards'])) {
                                            $blobData = base64_decode($chFinancialReport['chapter_awards']);
                                            $chapter_awards = unserialize($blobData);
                                        }
                                    @endphp

                                    @if ($chapter_awards === false)
                                        @elseif (is_array($chapter_awards) && count($chapter_awards) > 0)
                                            @foreach ($chapter_awards as $row)
                                                @php
                                                    $awardType = "Unknown";
                                                    foreach($allAwards as $award) {
                                                        if($award->id == $row['awards_type']) {
                                                            $awardType = $award->award_type;
                                                            break;
                                                        }
                                                    }
                                                    $approved = $row['awards_approved'];
                                                @endphp

                                                <label class="me-2">{{ $awardType }}:</label>
                                                <span class="badge {{ is_null($approved) ? 'bg-secondary' : ($approved == 1 ? 'bg-success' : 'bg-danger') }} fs-7">
                                                    {{ is_null($approved) ? 'Not Reviewed' : ($approved == 1 ? 'Approved' : 'Not Approved') }}
                                                </span>
                                            @endforeach
                                        @endif

                                    </div>
                                </div>
                            @endif

                            <div class="row">
                            @include('boards-new.partials.resources_accordion_eoy', ['resources' => $resources])
                            </div>

                          </div>
            </div>
            <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
            </div>
            <!-- /.col -->
       </div>
    </div>
    <!-- /.container- -->
@endsection
@section('customscript')
@if($userTypeId == \App\Enums\UserTypeEnum::COORD)
    @php $disableMode = 'disable-all'; @endphp
    @include('layouts.scripts.disablefields')
@endif
@endsection
