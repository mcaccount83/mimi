@extends('layouts.coordinator_theme')

@section('content')
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      International Chapter List
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">International Chapter List</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
      <div class="row">
		<div class="col-md-12">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">List of International Chapters</h3>
              <div class="box-tools">
                <div class="input-group input-group-sm" style="width: 250px;">
                </div>
              </div>
            </div>
            <!-- /.box-header -->

            <div class="box-body table-responsive">
              <table id="chapterlist" class="table table-bordered table-hover">
              <thead>
			    <tr>
					<th></th>
					<th>Conference</th>
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
                @foreach($intChapterList as $list)
                  <tr>
                        <td><a href="<?php echo url("/chapter/international/view/{$list->id}") ?>"><i class="fa fa-eye" aria-hidden="true"></i></a></td>
                        <td>{{ $list->cor_cid }}</td>
                        <td>{{ $list->state }}</td>
                        <td>{{ $list->name }}</td>
                        <td>{{ $list->ein }}</td>
                      <td>{{ $list->pre_fname }}</td>
                      <td>{{ $list->pre_lname }}</td>
                      <td><a href="mailto:{{ $list->pre_email }}">{{ $list->pre_email }}</a></td>
                      <td>{{ formatPhoneNumber($list->pre_phone) }}</td>
                      <td>{{ $list->cd_fname }} {{ $list->cd_lname }}</td>
                    </tr>
                  @endforeach
                  </tbody>
                </table>
            </div>

            <div class="box-body text-center">
            <a href="{{ route('export.intchapter') }}"><button class="btn btn-themeBlue margin" <?php if($countList ==0) echo "disabled";?>>Export Chapter List</button></a>
              </div>
            </div>
          <!-- /.box -->
        </div>
      </div>
    </section>
    <!-- Main content -->

    <!-- /.content -->

@endsection
