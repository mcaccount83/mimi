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
                        <h3 class="card-title dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
                            <th>Members</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reChapterList as $list)
                        @php
                            $due = $list->startMonth->month_short_name . ' ' . $list->next_renewal_year;
                            $overdue = (date('Y') * 12 + date('m')) - ($list->next_renewal_year * 12 + $list->start_month_id);
                        @endphp
                        <tr>
                            <td class="text-center align-middle">
                                @if ($conferenceCoordinatorCondition)
                                    <a href="{{ url("/payment/chapterpaymentedit/{$list->id}") }}"><i class="far fa-credit-card"></i></a>
                                @endif
                            </td>
                            <td class="text-center align-middle">
                                @if ($due && !$overdue)
                                    <a onclick="showChapterReRegEmailModal('{{ $list->name }}', {{ $list->id }})"><i class="far fa-envelope text-primary"></i></a>
                                @endif
                                @if ($overdue == 1)
                                    <a onclick="showChapterReRegLateEmailModal('{{ $list->name }}', {{ $list->id }})"><i class="far fa-envelope text-primary"></i></a>
                                @endif
                                @if ($overdue > 1)

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
                            <td>{{ $list->payments->rereg_notes }}</td>
                            <td @if ($overdue > 1) style="background-color: #dc3545; color: #ffffff;"
                                @elseif ($overdue == 1) style="background-color: #ffc107;"
                                @endif
                                data-sort="{{ $list->next_renewal_year . '-' . str_pad($list->start_month_id, 2, '0', STR_PAD_LEFT) }}">
                                {{ $due }}
                            </td>
                            <td><span class="date-mask">{{ $list->payments->rereg_date }}</span></td>
                            <td>{{ $list->payments->rereg_members }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
              </div>
          <!-- /.card-body -->
          <div class="col-sm-12">
            <div class="custom-control custom-switch">
                <input type="checkbox" name="showPrimary" id="showPrimary" class="custom-control-input" {{$checkBoxStatus}} onchange="showChPrimary()" />
                <label class="custom-control-label" for="showPrimary">Only show chapters I am primary for</label>
            </div>
        </div>
                @if ($coordinatorCondition && $assistRegionalCoordinatorCondition)
                    <div class="col-sm-12">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="showAllConf" id="showAllConf" class="custom-control-input" {{$checkBox3Status}} onchange="showChAllConf()" />
                            <label class="custom-control-label" for="showAllConf">Show All Chapters</label>
                        </div>
                    </div>
                @endif
                @if ($ITCondition || $einCondition)
                    <div class="col-sm-12">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="showAll" id="showAll" class="custom-control-input" {{$checkBox5Status}} onchange="showChAll()" />
                            <label class="custom-control-label" for="showAll">Show All International Chapters</label>
                        </div>
                    </div>
                @endif
                <div class="card-body text-center">
                    @if($conferenceCoordinatorCondition)
                        @if($checkBoxStatus || $checkBox3Status || $checkBox5Status)
                            <button class="btn bg-gradient-primary mb-3" disabled><i class="fas fa-envelope mr-2" ></i>Send Current Month Reminders</button>
                            <button class="btn bg-gradient-primary mb-3" disabled><i class="fas fa-envelope mr-2" ></i>Send One Month Late Notices</button>
                        @else
                            <a class="btn bg-gradient-primary mb-3" href="{{ route('payment.chapreregreminder') }}"><i class="fas fa-envelope mr-2" ></i>Send Current Month Reminders</a>
                            <a class="btn bg-gradient-primary mb-3" href="{{ route('payment.chaprereglatereminder') }}"><i class="fas fa-envelope mr-2" ></i>Send One Month Late Notices</a>
                        @endif
                        @if ($checkBox3Status)
                            <button class="btn bg-gradient-primary mb-3" onclick="startExport('reregoverdue', 'Overdue Re-Reg List')"><i class="fas fa-download mr-2" ></i>Export Overdue Re-Reg List</button>
                        @elseif ($checkBox5Status)
                            <button class="btn bg-gradient-primary mb-3" onclick="startExport('intreregoverdue', 'International Overdue Re-Reg List')"><i class="fas fa-download"></i>&nbsp; Export International Overdue Re-Reg List</button>
                        @else
                            <button class="btn bg-gradient-primary mb-3" onclick="startExport('reregoverdue', 'Overdue Re-Reg List')" disabled><i class="fas fa-download mr-2" ></i>Export Overdue Re-Reg List</button>
                        @endif
                    @endif
            </div>
        </div>
    </div>
    <!-- /.card -->
  </div>
  <!-- /.col -->
</div>
<!-- /.row -->
</div>
<!-- /.container-fluid -->
</section>
<!-- /.content -->
@endsection
