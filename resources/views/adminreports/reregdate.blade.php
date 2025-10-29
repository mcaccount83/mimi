@extends('layouts.coordinator_theme')

@section('page_title', 'Admin Reports')
@section('breadcrumb', 'Re-Registration Renewal Dates')

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
                                Re-Registration Dates
                            </h3>
                            @include('layouts.dropdown_menus.menu_reports_admin')
                        </div>
                    </div>
                 <!-- /.card-header -->
    <div class="card-body">
        <table id="chapterlist" class="table table-sm table-hover" >
              {{-- <table id="chapterlist_reRegDate" class="table table-bordered table-hover"> --}}
              <thead>
      			    <tr>
          			<th>Details</th>
                    <th>Conf/Reg</th>
          			<th>State</th>
                    <th>Name</th>
                    <th class="nosort" id="due_sort">Renewal Date</th>
                    <th>Last Paid</th>
                    <th>Members</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($chapterList as $list)
                  <tr>
                        <td class="text-center align-middle"><a href="{{ url("/adminreports/reregdate/{$list->id}") }}"><i class="fas fa-eye"></i></a></td>
                        <td>
                            @if ($list->region?->short_name != "None" )
                                {{ $list->conference->short_name }} / {{ $list->region?->short_name }}
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
						<td style="
                                    @php
                                        $due = $list->startMonth->month_long_name . ' ' . $list->next_renewal_year;
                                        $overdue = (date('Y') * 12 + date('m')) - ($list->next_renewal_year * 12 + $list->start_month_id);
                                        if ($overdue > 1) {
                                            echo 'background-color: #dc3545; color: #ffffff;';
                                        } elseif ($overdue == 1) {
                                            echo 'background-color: #ffc107;';
                                        }
                                    @endphp
                                " data-sort="{{ $list->next_renewal_year . '-' . str_pad($list->start_month_id, 2, '0', STR_PAD_LEFT) }}">
                                {{ $due }}
                        </td>
						<td><span class="date-mask">{{ $list->payments->rereg_date }}</span></td>
                        <td>{{ $list->payments->rereg_members }}</td>
                    </tr>
                  @endforeach
                  </tbody>
                </table>
            </div>

              @if ($ITCondition)
                    <div class="col-sm-12">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="showAll" id="showAll" class="custom-control-input" {{$checkBox5Status}} onchange="showAll()" />
                            <label class="custom-control-label" for="showAll">Show All International Chapters</label>
                        </div>
                    </div>
                @endif

            </div>
        </div>
      </div>
    </div>
    </section>
@endsection

@section('customscript')
<script>
document.addEventListener("DOMContentLoaded", function() {
    const dropdownItems = document.querySelectorAll(".dropdown-item");
    const currentPath = window.location.pathname;

    dropdownItems.forEach(item => {
        const itemPath = new URL(item.href).pathname;

        if (itemPath == currentPath) {
            item.classList.add("active");
        }
    });
});

function showAll() {
    var base_url = '{{ url("/adminreports/reregdate") }}';
    if ($("#showAll").prop("checked") == true) {
        window.location.href = base_url + '?{{ \App\Enums\ChapterCheckbox::INTERNATIONAL }}=yes';
    } else {
        window.location.href = base_url;
    }
}
</script>
@endsection
