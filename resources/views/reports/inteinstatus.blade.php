@extends('layouts.coordinator_theme')

@section('content')
<section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>International EIN Status Report</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">International EIN Status Report</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

    <!-- Main content -->
   <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">International EIN Status</h3>
            </div>
            <!-- /.card-header -->
        <div class="card-body">
              {{-- <table id="chapterlist_inteinStatus" class="table table-bordered table-hover"> --}}
                <table id="coordinatorlist"  class="table table-sm table-hover">
              <thead>
			    <tr>
                    <th>Notes</th>
                    <th>Conference</th>
                    <th>State</th>
                    <th>Name</th>
                    <th>Start Date</th>
                    <th>EIN</th>
                    <th>Letter Received</th>
                    <th>Letter Link</th>
                    <th>EIN/IRS Notes</th>
                </tr>
                </thead>
                <tbody>
                @foreach($chapterList as $list)
                  <tr>
                    <td>
                        <center><a href="{{ url("/chapter/einnotes/{$list->id}") }}"><i class="fas fa-pencil-alt"></i></a></center>
                    </td>
                        <td>{{ $list->conference }}</td>
						<td>{{ $list->state }}</td>
                        <td>{{ $list->name }}</td>
						<td data-sort="{{ $list->start_year . '-' . str_pad($list->start_month, 2, '0', STR_PAD_LEFT) }}">
                            {{ $list->start_month }} {{ $list->start_year }}
                        </td>
						<td>{{ $list->ein }}</td>
                        <td  @if($list->ein_letter_path != null)style="background-color: transparent;"
                             @else style="background-color:#dc3545; color: #ffffff;" @endif>
                            @if($list->ein_letter_path != null)
                                YES
                            @else
                                NO
                            @endif
                        </td>
						<td>
						    @if(empty($list->ein_letter_path))

						    @else
						    <a href="{{ $list->ein_letter_path }}" target="blank">{{ $list->name }} EIN Letter</a>
						    @endif</td>
                            <td>{{ $list->ein_notes }}</td>
			        </tr>
                  @endforeach
                  </tbody>
                </table>
            </div>
                <div class="card-body text-center">
                    <a href="{{ route('export.einstatus')}}"><button class="btn bg-gradient-primary"><i class="fas fa-download " ></i>&nbsp;&nbsp;&nbsp;Export EIN Status List</button></a>
                </div>
            </div>
             <!-- /.box -->
           </div>
         </div>
        </div>
       </section>
       <!-- Main content -->

       <!-- /.content -->
   @endsection

@section('customscript')

@endsection
