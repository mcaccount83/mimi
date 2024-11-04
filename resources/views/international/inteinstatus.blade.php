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
            <li class="breadcrumb-item"><a href="{{ route('coordinators.coorddashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
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
            <div class="card card-outline card-primary">
                <div class="card-header">
              <h3 class="card-title">International EIN Status</h3>
            </div>
            <!-- /.card-header -->
        <div class="card-body">
              {{-- <table id="chapterlist_inteinStatus" class="table table-bordered table-hover"> --}}
                <table id="chapterlist"  class="table table-sm table-hover">
              <thead>
			    <tr>
                    <th>Letter</th>
                    <th>Notes</th>
                    <th>Conference</th>
                    <th>State</th>
                    <th>Name</th>
                    <th>Start Date</th>
                    <th>EIN</th>
                    <th>Letter On File</th>
                    <th>EIN/IRS Notes</th>
                </tr>
                </thead>
                <tbody>
                @foreach($chapterList as $list)
                  <tr>
                        <td class="text-center align-middle">
                            @if($list->ein_letter_path != null)
                                <a href="{{ $list->ein_letter_path }}" target="_blank"><i class="fas fa-eye"></i></a>
                            @else
                                &nbsp; <!-- Placeholder to ensure the cell isn't completely empty -->
                            @endif
                        </td>
                        <td class="text-center align-middle">
                            <a href="{{ url("/international/einstatusview/{$list->id}") }}"><i class="fas fa-pencil-alt"></i></a>
                        </td>
                        <td>
                        @if ($list->reg != "None")
                                {{ $list->conf }} / {{ $list->reg }}
                            @else
                                {{ $list->conf }}
                            @endif
                        </td>
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
                        <td>{{ $list->ein_notes }}</td>
			        </tr>
                  @endforeach
                  </tbody>
                </table>
            </div>
                <div class="card-body text-center">
                    <a href="{{ route('export.einstatus')}}"><button class="btn bg-gradient-primary"><i class="fas fa-download " ></i>&nbsp;&nbsp;&nbsp;Export EIN Status List</button></a>
                    <a href="{{ route('export.irsfiling')}}"><button class="btn bg-gradient-primary"><i class="fas fa-download " ></i>&nbsp;&nbsp;&nbsp;Export Subordinate Filing List</button></a>

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
