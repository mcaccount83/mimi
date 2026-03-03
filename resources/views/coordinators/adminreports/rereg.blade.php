@extends('layouts.mimi_theme')

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
                            <h3 class="card-title dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Re-Registration Report
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
          			<th>ReReg<br>Details</th>
                    <th>Conf/Reg</th>
          			<th>State</th>
                    <th>Name</th>
                    <th class="nosort" id="due_sort">Renewal Date</th>
                    <th>Last Paid</th>
                            <th>Payment</th>
                    <th>Members</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($chapterList as $list)
                  <tr>
                        <td class="text-center align-middle"><a href="{{ url("/adminreports/reregedit/{$list->id}") }}"><i class="bi bi-credit-card-fill"></i></a></td>
                        <td>
                            @if ($list->state->conference_id > 0)
                                {{ $list->state->conference->short_name }} / {{ $list->state->region->short_name }}
                            @else
                                {{ $list->state->conference->short_name }}
                            @endif
                        </td>
                        <td>
                            @if($list->state_id < 52)
                                {{$list->state->state_short_name}}
                            @else
                                {{$list->state->country?->short_name}}
                            @endif
                        </td>
                        <td>{{ $list->name }}</td>
						<td style="
                                    @php
                                        $due = $list->startMonth->month_long_name . ' ' . $list->next_renewal_year;
                                        $overdue = ($currentYear * 12 + $currentMonth) - ($list->next_renewal_year * 12 + $list->start_month_id);
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
                        <td>
                            @if( $list->payments->rereg_payment != null )
                            ${{ $list->payments->rereg_payment }}
                            @endif
                        </td>
                        <td>{{ $list->payments->rereg_members }}</td>
                    </tr>
                  @endforeach
                  </tbody>
                </table>
           </div>
            <!-- /.card-body -->

            <div class="card-body">
              @if ($ITCondition)
                    <div class="col-sm-12">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="showIntl" id="showIntl" class="form-check-input" {{ $checkBox51Status ? 'checked' : '' }} onchange="showIntl()" />
                            <label class="form-check-label" for="showIntl">Show All International Chapters</label>
                        </div>
                    </div>
                @endif
                 </div>
            <!-- /.card-body for checkboxes -->

            <div class="card-body text-center mt-3">
            </div>
            <!-- /.card-body for buttons -->

        </div>
        <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </div>
  <!-- /.container-fluid -->
</section>
@endsection
