<!-- resources/views/payment-logs/index.blade.php -->
@extends('layouts.mimi_theme')

@section('page_title', 'Admin Reports')
@section('breadcrumb', 'Payment List')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid table-container">
            <div class="row">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <div class="dropdown">
                            <h3 class="card-title dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Donation Log Report
                            </h3>
                            @include('layouts.dropdown_menus.menu_reports_admin')
                        </div>
                    </div>
            <!-- /.card-header -->
    <!-- /.card-header -->
    <div class="card-body">
                    <table id="chapterlist" class="table table-sm table-hover" >
                       <thead>
                        <tr>
                            <th>Conf/Reg</th>
                            <th>State</th>
                            <th>Name</th>
                            <th>Donation Type</th>
                            <th>Amount</th>
                            <th>Date Paid</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($donationsList as $list)

                        <tr>

                            <td>
                                @if ($list->chapter->region->short_name != "None")
                                    {{ $list->chapter->conference->short_name }} / {{ $list->chapter->region->short_name }}
                                @else
                                    {{ $list->chapter->conference->short_name }}
                                @endif
                            </td>
                            <td>
                                @if($list->chapter->state_id < 52)
                                    {{$list->chapter->state->state_short_name}}
                                @else
                                    {{$list->chapter->country->short_name}}
                                @endif
                            </td>
                            <td>{{ $list->chapter->name }}</td>
                            <td>
                                @if ($list->payment_type == 'm2m')
                                M2M Donation
                                @elseif ($list->payment_type == 'sustaining')
                                Sustaining Chapter
                                @endif
                            </td>
                            <td>${{ $list->payment_amount }}</td>
                            <td>{{ $list->payment_date->format('Y-m-d') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    </table>
                </div>

                <div class="col-sm-12">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="showM2M" id="showM2M" class="form-check-input" {{ $checkBox8Status ? 'checked' : '' }} onchange="showM2M()" />
                            <label class="form-check-label" for="showM2M">Show Only M2M Donations</label>
                        </div>
                    </div>

                @if ($ITCondition)
                    <div class="col-sm-12">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="showIntlM2M" id="showIntlM2M" class="form-check-input" {{ $checkBox58Status ? 'checked' : '' }} onchange="showIntlM2M()" />
                            <label class="form-check-label" for="showIntlM2M">Show Only M2M International Donations</label>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="showIntl" id="showIntl" class="form-check-input" {{ $checkBox51Status ? 'checked' : '' }} onchange="showIntl()" />
                            <label class="form-check-label" for="showIntl">Show All International Donations</label>
                        </div>
                    </div>
                @endif
            <div class="card-body text-center mt-3">
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


