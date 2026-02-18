@extends('layouts.coordinator_theme')

@section('page_title', 'Payments/Donations')
@section('breadcrumb', 'Re-Registration Payments')

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
                        Re-Registration Payments
                    </h3>
                    @include('layouts.dropdown_menus.menu_payment')
                </div>
            </div>
                <!-- /.card-header -->
            <div class="card-body">
                <table id="chapterlist" class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th>Payment</th>
                            <th>Send</th>
                            <th>Conf/Reg</th>
                            <th>State</th>
                            <th>Name</th>
                            <th>Re-Registration Notes</th>
                            <th>Due</th>
                            <th>Last Paid</th>
                            <th>Payment</th>
                            <th>Members</th>
                            <th>History</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reChapterList as $list)
                        @php
                            $due = $list->startMonth->month_short_name . ' ' . $list->next_renewal_year;
                            $overdue = $currentYear * 12 + $currentMonth - ($list->next_renewal_year * 12 + $list->start_month_id);
                        @endphp
                        <tr>
                            <td class="text-center align-middle">
                                @if ($conferenceCoordinatorCondition)
                                    <a href="{{ url("/payment/chapterpaymentedit/{$list->id}") }}"><i class="far fa-credit-card"></i></a>
                                @endif
                            </td>
                            <td class="text-center align-middle">
                                @if ($due && !$overdue)
                                    <a onclick="showChapterReRegEmailModal('{{ $list->name }}', {{ $list->id }})"><i class="bi bi-envelope text-primary"></i></a>
                                @endif
                                @if ($overdue == 1)
                                    <a onclick="showChapterReRegLateEmailModal('{{ $list->name }}', {{ $list->id }})"><i class="bi bi-envelope text-primary"></i></a>
                                @endif
                                @if ($overdue > 1)

                                @endif
                            </td>
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
                            <td>{{ $list->payments->rereg_notes }}</td>
                            <td @if ($overdue > 1) style="background-color: #dc3545; color: #ffffff;"
                                @elseif ($overdue == 1) style="background-color: #ffc107;"
                                @endif
                                data-sort="{{ $list->next_renewal_year . '-' . str_pad($list->start_month_id, 2, '0', STR_PAD_LEFT) }}">
                                {{ $due }}
                            </td>
                            <td><span class="date-mask">{{ $list->payments->rereg_date }}</span></td>
                            <td>
                                @if( $list->payments->rereg_payment != null )
                                ${{ $list->payments->rereg_payment }}
                                @endif
                            </td>
                            <td>{{ $list->payments->rereg_members }}</td>
                             <th>
                                <a href="{{ url("/payment/chapterpaymenthistory/{$list->id}") }}"><i class="fas fa-file-invoice-dollar "></i></a>
                            </th>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
              </div>
          <!-- /.card-body -->

            <div class="card-body">
                <div class="col-sm-12">
                    <div class="form-check form-switch">
                        <input type="checkbox" name="showPrimary" id="showPrimary" class="form-check-input" {{$checkBox1Status ? 'checked' : '' }} onchange="showPrimary()" />
                        <label class="form-check-label" for="showPrimary">Only show chapters I am primary for</label>
                    </div>
                </div>
                @if ($coordinatorCondition && $assistRegionalCoordinatorCondition)
                <div class="col-sm-12">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="showConfReg" id="showConfReg" class="form-check-input" {{$checkBox3Status ? 'checked' : '' }} onchange="showConfReg()" />
                            @if ($assistConferenceCoordinatorCondition)
                                    <label class="form-check-label" for="showConfReg">Show All Chapters in Conference (Export Available)</label>
                                @else
                            <label class="form-check-label" for="showConfReg">Show All Chapters in Region (Export Available)</label>
                            @endif
                        </div>
                    </div>
                @endif
                @if ($ITCondition || $einCondition)
                <div class="col-sm-12">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="showIntlReReg" id="showIntlReReg" class="form-check-input" {{$checkBox56Status ? 'checked' : '' }} onchange="showIntlReReg()" />
                            <label class="form-check-label" for="showIntlReReg">Show International Chapters Due</label>
                        </div>
                    </div>
                <div class="col-sm-12">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="showIntl" id="showIntl" class="form-check-input" {{$checkBox51Status ? 'checked' : '' }} onchange="showIntl()" />
                            <label class="form-check-label" for="showIntl">Show All International Chapters (Export Available)</label>
                        </div>
                    </div>
                @endif
                  </div>
            <!-- /.card-body for checkboxes -->

                <div class="card-body text-center mt-3">
                    @if($conferenceCoordinatorCondition)
                        @if(!$checkBox1Status && !$checkBox3Status && !$checkBox51Status && !$checkBox56Status)
                            <a class="btn btn-primary bg-gradient mb-2" href="{{ route('payment.chapreregreminder') }}"><i class="bi bi-envelope-fill me-2"></i>Send Current Month Reminders</a>
                            <a class="btn btn-primary bg-gradient mb-2" href="{{ route('payment.chaprereglatereminder') }}"><i class="bi bi-envelope-fill me-2"></i>Send One Month Late Notices</a>
                        @endif
                        @if ($checkBox3Status)
                            <button class="btn btn-primary bg-gradient mb-2" onclick="startExport('reregoverdue', 'Overdue Re-Reg List')"><i class="bi bi-download me-2"></i>Export Overdue Re-Reg List</button>
                        @elseif ($checkBox51Status)
                            <button class="btn btn-primary bg-gradient mb-2" onclick="startExport('intreregoverdue', 'International Overdue Re-Reg List')"><i class="bi bi-download me-2"></i>Export International Overdue Re-Reg List</button>
                        {{-- @else
                            <button class="btn btn-primary bg-gradient mb-2 disabled" onclick="startExport('reregoverdue', 'Overdue Re-Reg List')" disabled><i class="bi bi-download me-2"></i></i>Export Overdue Re-Reg List</button> --}}
                        @endif
                    @endif
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
