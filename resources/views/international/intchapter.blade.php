@extends('layouts.coordinator_theme')

@section('content')
<section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>International Chapter List</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('coordinators.coorddashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">International Chapter List</li>
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
                  <h3 class="card-title">List of International Chapters</h3>
                </div>
                <!-- /.card-header -->
            <div class="card-body">
              <table id="chapterlist" class="table table-sm table-hover" >
              <thead>
			    <tr>
					<th>Details</th>
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
                @foreach($intChapterList as $list)
                  <tr>
                    <td><center><a href="<?php echo url("/international/chapterview/{$list->id}") ?>"><i class="fa fa-eye"></i></a></center></td>
                    <td>{{ $list->cor_cid }}</td>
                    <td>{{ $list->state }}</td>
                    <td>{{ $list->name }}</td>
                    <td>{{ $list->ein }}</td>
                    <td>{{ $list->pre_fname }}</td>
                    <td>{{ $list->pre_lname }}</td>
                    <td><a href="mailto:{{ $list->pre_email }}">{{ $list->pre_email }}</a></td>
                    <td><span class="phone-mask">{{ $list->pre_phone }}</span></td>
                    <td>{{ $list->cd_fname }} {{ $list->cd_lname }}</td>
                    </tr>
                  @endforeach
                  </tbody>
                </table>
            </div>

            <div class="card-body text-center">
            <a href="{{ route('export.intchapter') }}"><button class="btn bg-gradient-primary" <?php if($countList ==0) echo "disabled";?>><i class="fas fa-download" ></i>&nbsp;&nbsp;&nbsp;Export Chapter List</button></a>
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

</script>
@endsection
