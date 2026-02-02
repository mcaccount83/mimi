@extends('layouts.coordinator_theme')

@section('page_title', 'Chapters')
@section('breadcrumb', 'Inquiry Applicaiton List')

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
                            Inquiries Received List
                        </h3>
                        @include('layouts.dropdown_menus.menu_inquiries')
                    </div>
                </div>
                <!-- /.card-header -->
            <div class="card-body">
                <table id="chapterlist" class="table table-sm table-hover" >
                <thead>
                  <tr>
                    <th>Details</th>
                    <th>Date</th>
                    <th>Conf/Reg</th>
                    <th>State</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Chapter Available</th>
                    <th>Response</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                    @foreach($inquiryList as $list)
                        <tr id="inquiry-{{ $list->id }}">
                            <td class="text-center align-middle"><a href="{{ url("/inquiries/inquiryapplicationedit/{$list->id}") }}"><i class="fas fa-eye"></i></a></td>
                                <td>{{ $list->created_at->format('m-d-Y') }}</td>
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
                            <td>{{ $list->inquiry_first_name }} {{ $list->inquiry_last_name }}</td>
                            <td class="email-column">
                                <a href="mailto:{{ $list->inquiry_email }}">{{ $list->inquiry_email }}</a>
                            </td>
                            <td>
                                @if($list->available == '1') YES - {{ $list->chapter?->name }}
                                @elseif($list->available == '0') NO CHAPTER
                                @else
                                @endif
                            </td>
                            <td @if($list->response == '1') style="background-color: #transparent;" @else style="background-color:#dc3545; color: #ffffff;" @endif>
                                @if($list->response == '1') YES @else NO @endif
                            </td>
                            <td></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
                @if (($coordinatorCondition && $conferenceCoordinatorCondition) || $inquiriesCondition)
                     <div class="col-sm-12">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="showInq" id="showInq" class="custom-control-input"
                                {{ $checkBox7Status ? 'checked' : '' }} onchange="showInqOut()" />
                            <label class="custom-control-label" for="showInq">Show Only Outstanding Inquiries</label>
                        </div>
                    </div>
                 @endif

                @if ($inquiriesInternationalCondition || $ITCondition)
                    <div class="col-sm-12">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="showInqAll" id="showInqAll" class="custom-control-input"
                                {{ $checkBox8Status ? 'checked' : '' }} onchange="showInqOutAll()" />
                            <label class="custom-control-label" for="showInqAll">Show Only Outstanding International Inquiries</label>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="showAll" id="showAll" class="custom-control-input"
                                {{ $checkBox5Status ? 'checked' : '' }} onchange="showInqAll()" />
                            <label class="custom-control-label" for="showAll">Show All International Inquiries</label>
                        </div>
                    </div>
                @endif
                <div class="card-body text-center">

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
