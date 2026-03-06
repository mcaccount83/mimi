@extends('layouts.mimi_theme')

@section('page_title', 'Admin Reports')
@section('breadcrumb', 'Grant Requests')

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
                                Grant Request Report
                            </h3>
                            @include('layouts.dropdown_menus.menu_payment')
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
                    <th>Amount Awarded</th>
                    <th>Board Contact</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($grantList as $list)
                  <tr>
                        <td class="text-center align-middle"><a href="{{ url("/payment/grantdetailsedit/{$list->id}") }}"><i class="bi bi-eye"></i></a></td>
                        <td>
                            @if ($list->chapterstate->region?->short_name != "None" )
                                {{ $list->chapterstate->conference->short_name }} / {{ $list->chapterstate->region?->short_name }}
                            @else
                                {{ $list->chapterstate->conference->short_name }}
                            @endif
                        </td>
                        <td>
                            {{$list->chapterstate->state_short_name}}
                            {{-- @if($list->chapters->state_id < 52)
                                {{$list->chapters->state->state_short_name}}
                            @else
                                {{$list->chapters->country->short_name}}
                            @endif --}}
                        </td>
                        <td>{{ $list->chapters?->name }}</td>
                        <td>{{ $list->first_name }} {{ $list->last_name }}</td>
                        <td>
                            @if($list->submitted == '1') Submitted | <span class="date-mask">{{ $list->submitted_at}}</span> @else Draft @endif
                        </td>
                         <td>
                            @if($list->grant_approved == '1') Approved
                            @elseif($list->grant_approved == '0') Declined
                             @else No Decision Made @endif
                        </td>
                        <td>
                             @if( $list->amount_awarded != null )
                                ${{ $list->amount_awarded }}
                            @endif
                        </td>
                        <td><a href="mailto:{{ $list->board_email }}">{{ $list->board_email }}</a></td>
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
                            <input type="checkbox" name="showIntl" id="showIntl" class="form-check-input"
                                {{ $checkBox51Status ? 'checked' : '' }} onchange="showIntl()" />
                            <label class="form-check-label" for="showIntl">Show All International Requests</label>
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

