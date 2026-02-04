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
                    <th>Member In Need</th>
                    <th>Grant Submitted</th>
                    <th>Grant Status</th>
                    <th>Member Notified</th>
                    <th>Chapter Notified</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($grantList as $list)
                  <tr>
                        <td class="text-center align-middle"><a href="{{ url("/adminreports/grantdetailsedit/{$list->id}") }}"><i class="fas fa-eye"></i></a></td>
                        <td>
                            @if ($list->chapters->region?->short_name != "None" )
                                {{ $list->chapters->conference->short_name }} / {{ $list->chapters->region?->short_name }}
                            @else
                                {{ $list->chapters->conference->short_name }}
                            @endif
                        </td>
                        <td>
                            @if($list->chapters->state_id < 52)
                                {{$list->chapters->state->state_short_name}}
                            @else
                                {{$list->chapters->country->short_name}}
                            @endif
                        </td>
                        <td>{{ $list->chapters->name }}</td>
                        <td>{{ $list->first_name }} {{ $list->last_name }}</td>
                        <td>
                            @if($list->submitted == '1') Submitted @else Draft @endif
                        </td>
                         <td>
                            @if($list->grant_approved == '1') Approved
                            @elseif($list->grant_approved == '1') Declined
                             @else
                              @endif
                        </td>
                         <td>
                            @if($list->mbr_notified == '1') Yes @else No @endif
                        </td>
                        <td>
                            @if($list->ch_notified == '1') Yes @else No @endif
                        </td>
                    </tr>
                  @endforeach
                  </tbody>
                </table>
            </div>

              @if ($ITCondition)
                    <div class="col-sm-12">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="showAll" id="showAll" class="custom-control-input" {{$checkBox5Status}} onchange="showChAll()" />
                            <label class="custom-control-label" for="showAll">Show All International Chapters</label>
                        </div>
                    </div>
                @endif
            <div class="card-body text-center">
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
