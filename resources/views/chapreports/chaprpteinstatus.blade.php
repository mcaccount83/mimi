@extends('layouts.coordinator_theme')

@section('page_title', 'Chapter Reports')
@section('breadcrumb', 'IRS Status Report')

@section('content')
   <!-- Main content -->
   <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                <div class="dropdown">
                    <h3 class="card-title dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        IRS Status Report
                    </h3>
                    @include('layouts.dropdown_menus.menu_reports_chap')
                </div>
            </div>
            <!-- /.card-header -->
        <div class="card-body">
            <table id="chapterlist" class="table table-sm table-hover" >
              <thead>
			    <tr>
                    <th>Details</th>
                    <th>Letter</th>
                    <th>Conf/Reg</th>
                    <th>State</th>
                    <th>Name</th>
                    <th>Start Date</th>
                    <th>EIN</th>
                    <th>Letter On File</th>
                    <th>EIN Notes</th>
                </tr>
                </thead>
                <tbody>
                @foreach($chapterList as $list)
                    <tr >
                        <td class="text-center align-middle">
                            @if ($conferenceCoordinatorCondition)
                                <a href="{{ url("/chapterirsedit/{$list->id}") }}"><i class="fas fa-eye"></i></a>
                            @else
                                &nbsp; <!-- Placeholder to ensure the cell isn't completely empty -->
                            @endif
                        </td>
                        <td class="text-center align-middle">
                            @if ($list->documents->ein_letter_path != null)
                                <a href="{{ $list->documents->ein_letter_path }}"
                                    onclick="event.preventDefault(); openPdfViewer('{{ $list->documents->ein_letter_path }}');">
                                    <i class="far fa-file-pdf"></i>
                                    </a>
                            @else
                                &nbsp; <!-- Placeholder to ensure the cell isn't completely empty -->
                            @endif
                        </td>
                        <td>
                            @if ($list->region->short_name != "None")
                                {{ $list->conference->short_name }} / {{ $list->region->short_name }}
                            @else
                                {{ $list->conference->short_name }}
                            @endif
                        </td>
                        <td>
                                @if($list->state_id < 52)
                                    {{$list->state->state_short_name}}
                                @else
                                    {{$list->country->short_name}}
                                @endif
                            </td>
                        <td>{{ $list->name }}</td>
                        <td data-sort="{{ $list->start_year . '-' . str_pad($list->start_month_id, 2, '0', STR_PAD_LEFT) }}">
                            {{ $list->startMonth->month_short_name }} {{ $list->start_year }}
                        </td>
						<td>{{ $list->ein }}</td>
                        <td @if($list->documents->ein_letter_path != null)style="background-color: transparent;"
                            @else style="background-color:#dc3545; color: #ffffff;" @endif>
                            @if($list->documents->ein_letter_path != null)
                                YES
                            @else
                                NO
                            @endif
                        </td>
                        <td>{{ $list->documents->ein_notes }}</td>
			        </tr>
                  @endforeach
                  </tbody>
                </table>
            </div>
            <!-- /.card-body -->
            <div class="col-sm-12">
                <div class="custom-control custom-switch">
                    <input type="checkbox" name="showPrimary" id="showPrimary" class="custom-control-input" {{$checkBoxStatus}} onchange="showPrimary()" />
                    <label class="custom-control-label" for="showPrimary">Only show chapters I am primary for</label>
                </div>
            </div>
                <div class="card-body text-center">
                    @if ($assistConferenceCoordinatorCondition)
                        <button class="btn bg-gradient-primary" onclick="startExport('zapchapter', 'Zapped Chapter List')"><i class="fas fa-download mr-2" ></i>Export EIN Status List</button>
                    @endif
                    </div>

              </div>
            </div>

           </div>
          <!-- /.box -->
        </div>
    </section>

    <!-- /.content -->

@endsection
@section('customscript')
<script>
function showPrimary() {
    var base_url = '{{ url("/chapterreports/einstatus") }}';

    if ($("#showPrimary").prop("checked") == true) {
        window.location.href = base_url + '?check=yes';
    } else {
        window.location.href = base_url;
    }
}

document.addEventListener("DOMContentLoaded", function() {
    const dropdownItems = document.querySelectorAll(".dropdown-item");
    const currentPath = window.location.pathname;

    dropdownItems.forEach(item => {
        const itemPath = new URL(item.href).pathname;

        if (itemPath === currentPath) {
            item.classList.add("active");
        }
    });
});

</script>
@endsection
