@extends('layouts.coordinator_theme')

@section('content')
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      EIN Status Report
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">EIN Status Report</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
		<div class="col-md-12">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">EIN Status</h3>

            </div>
            <!-- /.box-header -->

            <div class="box-body table-responsive">
              <table id="chapterlist_inteinStatus" class="table table-bordered table-hover">
              <thead>
			    <tr>
			      <th>Conference</th>
				  <th>State</th>
                  <th>Name</th>
                  <th>Start Date</th>
                    <th>EIN</th>
                    <th>Letter Received</th>
                    <th>Letter Link</th>
                </tr>
                </thead>
                <tbody>
                @foreach($chapterList as $list)
                  <tr>
                        <td>{{ $list->conference }}</td>
						<td>{{ $list->state }}</td>
                        <td>{{ $list->name }}</td>
						<td>{{ $list->start_month }} {{ $list->start_year }}</td>
						<td>{{ $list->ein }}</td>
                        <td style="background-color: @if($list->ein_letter_path != null) transparent; @else #FF000050; @endif;">
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
			        </tr>
                  @endforeach
                  </tbody>
                </table>
<div class="box-body text-center"><a href="{{ route('export.einstatus')}}"><button class="btn btn-themeBlue margin">Export EIN Status List</button></a>
            </div>
                </div>
              </div>
              </div>
            </div>

           </div>

          <!-- /.box -->
        </div>
      </div>
    </section>
    <!-- Main content -->

    <!-- /.content -->

@endsection
@section('customscript')

@endsection
