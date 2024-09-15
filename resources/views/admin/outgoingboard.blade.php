@extends('layouts.coordinator_theme')

@section('content')
 <!-- Content Header (Page header) -->
 <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Outgoing Board Report&nbsp;<small>(Access to Financial Report Only)</small></h1>
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
                  <th>Chapter</th>
                  <th>Name</th>
                  <th>Email</th>
                <th>User Type</th>

                </tr>
                </thead>
                <tbody>
                @foreach($OutgoingBoard as $list)
                  <tr>
                    <td>{{ $list->chapter_name }}, {{ $list->chapter_state }}</td>
                        <td>{{ $list->first_name }} {{ $list->last_name }}</td>
                        <td>{{ $list->email }}</td>
                        <td>{{ $list->user_type }}</td>
			        </tr>
                  @endforeach
                  </tbody>
                </table>
            </div>
            <!-- /.card-body -->
            {{-- <div class="col-sm-12">
                <div class="custom-control custom-switch">
                    <input type="checkbox" name="showPrimary" id="showPrimary" class="custom-control-input" {{$checkBoxStatus}} onchange="showPrimary()" />
                    <label class="custom-control-label" for="showPrimary">Only Show Outgoing Board Members with no User Account</label>
                </div>
            </div> --}}
            {{-- <div class="card-body text-center">
                <p>Clearing the table will remove user access to Financial Reports<br>
                <span style="color: red;">
                    This CANNOT be undone!</span></p>
				    <button type="button" id="update-outgoing" class="btn bg-gradient-primary" <?php if($countList ==0) echo "disabled";?>><i class="fas fa-user-times" ></i>&nbsp;&nbsp;&nbsp;Clear Outgoing Board Members Table</button>
             </div> --}}
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
// $(document).ready(function(){
//     var base_url = '{{ url("/adminreports/updateoutgoingboard") }}';

//     $("#update-outgoing").click(function() {
//         $.ajax({
//             url: base_url,
//             type: 'POST',
//             data: {
//                 _token: '{{ csrf_token() }}', // Include CSRF token for security
//             },
//             success: function(response) {
//                 // Handle success (e.g., show a message or refresh the page)
//                 alert('Outgoing board members updated successfully.');
//                 // Optionally, reload the page or redirect
//                 location.reload();
//             },
//             error: function(xhr) {
//                 // Handle errors
//                 alert('An error occurred while updating the outgoing board members.');
//             }
//         });
//     });
// });

// function showPrimary() {
// var base_url = '{{ url("/adminreports/outgoingboard") }}';

//     if ($("#showPrimary").prop("checked") == true) {
//         window.location.href = base_url + '?check=yes';
//     } else {
//         window.location.href = base_url;
//     }
// }
</script>
@endsection
