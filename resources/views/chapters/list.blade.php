@extends('layouts.coordinator_theme')

@section('content')
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Chapter List
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Chapter List</li>
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

    <!-- Main content -->
    <section class="content">
      <div class="row">
		<div class="col-md-12">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">List of Chapters</h3>
             </div>
            <!-- /.box-header -->

            <div class="box-body table-responsive">
              <table id="chapterlist" class="table table-bordered table-hover">
                <thead>
                  <tr>
                    <th>Details</th>
                    <th>Email</th>
                    <th>State</th>
                    <th>Name</th>
                    <th>EIN</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Primary Coordinator</th>

                  </tr>
                </thead>
                <tbody>
                    @if (!function_exists('formatPhoneNumber'))
                    <?php
                    function formatPhoneNumber($phoneNumber)
                    {
                        // Remove non-numeric characters from the phone number
                        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

                        // Format the phone number as xxx-xxx-xxxx
                        return substr($phoneNumber, 0, 3) . '-' . substr($phoneNumber, 3, 3) . '-' . substr($phoneNumber, 6);
                    }
                    ?>
                @endif
                @foreach($chapterList as $list)
                @php
                    $emailDetails = app('App\Http\Controllers\ChapterController')->getEmailDetails($list->id);
                    $emailListCord = $emailDetails['emailListCord'];
                    $cc_string = $emailDetails['cc_string'];
                @endphp
                <tr>
                <td><center><a href="<?php echo url("/chapter/edit/{$list->id}") ?>"><i class="fa fa-edit fa-lg" aria-hidden="true"></i></a></center></td>
                <td><center><a href="mailto:{{ $emailListCord }}{{ $cc_string }}&subject=MOMS Club of {{ $list->name }}, {{ $list->state }}"><i class="fa fa-envelope-o fa-lg" aria-hidden="true"></i></a></center></td>
                      <td>{{ $list->state }}</td>
                      <td>{{ $list->name }}</td>
                      <td>{{ $list->ein }}</td>
                      <td>{{ $list->bor_f_name }}</td>
                      <td>{{ $list->bor_l_name }}</td>
                      <td><a href="mailto:{{ $list->bor_email }}">{{ $list->bor_email }}</a></td>
                      <td>{{ formatPhoneNumber($list->phone) }}</td>
                      <td>{{ $list->cor_f_name }} {{ $list->cor_l_name }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            <div class="radio-chk labelcheck">
              <div class="col-sm-6 col-xs-12">
                <div class="form-group">
                    <label style="display: block;"><input type="checkbox" name="showPrimary" id="showPrimary" class="ios-switch green bigswitch" {{$checkBoxStatus}} onchange="showPrimary()" /><div><div></div></div>
                    </label>
                  <span> Only show chapters I am primary for</span>
                </div>
              </div>
              </div>
            </div>

            <div class="box-body text-center">
            <?php if (Session::get('positionid') >=5 && Session::get('positionid') <=7){ ?>
              <a class="btn btn-themeBlue margin" href="{{ route('chapters.create') }}"><i class="fa fa-plus fa-fw" aria-hidden="true" ></i>&nbsp; Add New Chapter</a>
			<?php }?>
			<?php
			 if($checkBoxStatus){ ?>
				<button class="btn btn-themeBlue margin" disabled>Export Chapter List</button>
			<?php
			 }
			 else{ ?>
				<a href="{{ route('export.chapter','0') }}"><button class="btn btn-themeBlue margin" <?php if($countList ==0) echo "disabled";?>><i class="fa fa-download fa-fw" aria-hidden="true" ></i>&nbsp; Export Chapter List</button></a>
			 <?php } ?>

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
var base_url = '{{ url("/chapter/list") }}';

    if ($("#showPrimary").prop("checked") == true) {
        window.location.href = base_url + '?check=yes';
    } else {
        window.location.href = base_url;
    }
}
</script>
@endsection
