@extends('layouts.coordinator_theme')

@section('content')
<section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Chapter Report</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('coordinators.coorddashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">Social Media Report</li>
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
                <div class="dropdown">
                    <h3 class="card-title dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Social Media Report
                    </h3>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="{{ route('chapreports.chaprptchapterstatus') }}">Chapter Status Report</a>
                        <a class="dropdown-item" href="{{ route('chapreports.chaprpteinstatus') }}">EIN Status Report</a>
                        <a class="dropdown-item" href="{{ route('chapreports.chaprptnewchapters') }}">New Chapter Report</a>
                        <a class="dropdown-item" href="{{ route('chapreports.chaprptlargechapters') }}">Large Chapter Report</a>
                        <a class="dropdown-item" href="{{ route('chapreports.chaprptprobation') }}">Chapter Probation Report</a>
                        <a class="dropdown-item" href="{{ route('chapreports.chaprptsocialmedia') }}">Social Media Report</a>
                        <a class="dropdown-item" href="{{ route('chapreports.chaprptcoordinators') }}">Chapter Coordinators Report</a>
                    </div>
                </div>
            </div>
            <!-- /.card-header -->
        <div class="card-body">
            <table id="chapterlist" class="table table-sm table-hover" >
              <thead>
			    <tr>
                    <th></th>
                    <th>Conf/Reg>
                  <th>State</th>
                  <th>Name</th>
                    <th>Facebook</th>
                    <th>Twitter</th>
                    <th>Instagram</th>
                </tr>
                </thead>
                <tbody>
                @foreach($chapterList as $list)
                  <tr>
                    <td></td>
                    <td>
                        @if ($list->reg != "None")
                            {{ $list->conf }} / {{ $list->reg }}
                        @else
                            {{ $list->conf }}
                        @endif
                    </td>
						<td>{{ $list->state }}</td>
                        <td>{{ $list->name }}</td>
						<td>{{ $list->social1 }}</td>
						<td>{{ $list->social2 }}</td>
						<td>{{ $list->social3 }}</td>
			        </tr>
                  @endforeach
                  </tbody>
                </table>
                </div>
                <div class="card-body text-center">&nbsp;</div>
            </div>
              </div>
              </div>
            </div>
    </section>
    <!-- /.content -->

@endsection
@section('customscript')
<script>
document.addEventListener("DOMContentLoaded", function() {
    const dropdownItems = document.querySelectorAll(".dropdown-item");
    const currentPath = window.location.pathname;

    dropdownItems.forEach(item => {
        const itemPath = new URL(item.href).pathname;

        if (itemPath === currentPath) {
            item.classList.add("active");
        }
    });
});

</script>
@endsection
