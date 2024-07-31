@extends('layouts.coordinator_theme')

@section('content')
 <!-- Content Header (Page header) -->
 <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Outgoing Board Report</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">Outgoing Board Report</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

    @if ($message = Session::get('success'))
      <div class="alert alert-success">
		<button type="button" class="close" data-dismiss="alert">×</button>
         <p>{{ $message }}</p>
      </div>
    @endif
	 @if ($message = Session::get('fail'))
      <div class="alert alert-danger">
		<button type="button" class="close" data-dismiss="alert">×</button>
         <p>{{ $message }}</p>
      </div>
    @endif
    @if ($message = Session::get('info'))
    <div class="alert alert-warning">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <p>{{ $message }}</p>
    </div>
@endif

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Report of Outgoing Board Members</h3>
                    </div>
                     <!-- /.card-header -->
        <div class="card-body">
            <table id="chapterlist" class="table table-sm table-hover" >
              <thead>
			    <tr>
                  <th>Chapter ID</th>
                  <th>Name</th>
                  <th>Email</th>
                <th>User Type</th>

                </tr>
                </thead>
                <tbody>
                @foreach($OutgoingBoard as $list)
                  <tr>
                    <td>{{ $list->chapter_id }}</td>
                        <td>{{ $list->first_name }} {{ $list->last_name }}</td>
                        <td>{{ $list->email }}</td>
                        <td>{{ $list->user_type }}</td>
			        </tr>
                  @endforeach
                  </tbody>
                </table>
            </div>
            <!-- /.card-body -->
            <div class="col-sm-12">
                <div class="custom-control custom-switch">
                    <input type="checkbox" name="showPrimary" id="showPrimary" class="custom-control-input" {{$checkBoxStatus}} onchange="showPrimary()" />
                    <label class="custom-control-label" for="showPrimary">Only Show Outgoing Board Members with no User Account</label>
                </div>
            </div>
            <div class="card-body text-center">
                    <button type="button" id="outgoing-active" class="btn bg-gradient-primary" onclick="return activateOutgoing()"><i class="fas fa-play " ></i>&nbsp;&nbsp;&nbsp;Update Outgoing Board Members</button>
            </div>
        </div>
          <!-- /.box -->
        </div>
      </div>
    </div>
    </section>
    <!-- /.content -->

@endsection
@section('customscript')
<script>

function showPrimary() {
var base_url = '{{ url("/adminreports/outgoingboard") }}';

    if ($("#showPrimary").prop("checked") == true) {
        window.location.href = base_url + '?check=yes';
    } else {
        window.location.href = base_url;
    }
}

function activateOutgoing(){
        $.ajax({
        url: '{{ route('report.outgoingactivate') }}',
        type: 'POST',
        data: { _token: '{{csrf_token()}}' },
        success: function(response) {
                window.location.href = "{{ route('report.outgoingboard') }}";

        },
        error: function (jqXHR, exception) {
        }
    });
}

</script>
@endsection
