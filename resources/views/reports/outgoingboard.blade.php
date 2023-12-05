@extends('layouts.coordinator_theme')

@section('content')
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      Outgoing Board Report
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Outgoing Board Report</li>
      </ol>
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
    @csrf
    <section class="content">
      <div class="row">
		<div class="col-md-12">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Report of Outgoing Board Members</h3>

            </div>
            <!-- /.box-header -->

            <div class="box-body table-responsive">
              <table id="chapterlist_zapped" class="table table-bordered table-hover">
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
                <div class="radio-chk labelcheck">
                    <div class="col-sm-6 col-xs-12">
                      <div class="form-group">
                          <label style="display: block;"><input type="checkbox" name="showPrimary" id="showPrimary" class="ios-switch green bigswitch" {{$checkBoxStatus}} onchange="showPrimary()" /><div><div></div></div>
                          </label>
                        <span> Only Show Outgoing Board Members with no User Account</span>
                      </div>
                    </div>
                    </div>
                <div class="modal-footer">
                <button type="button" id="outgoing-active" class="btn btn-themeBlue margin" onclick="return activateOutgoing()">Update Outgoing Board Members</button>
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
