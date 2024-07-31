@extends('layouts.coordinator_theme')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>Chapter List</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li class="breadcrumb-item active">Chapter List</li>
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

     <!-- Main content -->
     <section class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">List of Chapters</h3>
                </div>
                <!-- /.card-header -->
            <div class="card-body">
                <table id="chapterlist" class="table table-sm table-hover" >
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
                @foreach($chapterList as $list)
                @php
                    $emailDetails = app('App\Http\Controllers\ChapterController')->getEmailDetails($list->id);
                    $emailListCord = $emailDetails['emailListCord'];
                    $cc_string = $emailDetails['cc_string'];
                @endphp
                <tr>
                        <td class="text-center align-middle">
                            <a href="<?php echo url("/chapter/edit/{$list->id}") ?>"><i class="far fa-edit"></i></a></td>
                        <td class="text-center align-middle">
                            <a href="mailto:{{ $emailListCord }}{{ $cc_string }}&subject=MOMS Club of {{ $list->name }}, {{ $list->state }}"><i class="far fa-envelope"></i></a></td>
                      <td>{{ $list->state }}</td>
                      <td>{{ $list->name }}</td>
                      <td>{{ $list->ein }}</td>
                      <td>{{ $list->bor_f_name }}</td>
                      <td>{{ $list->bor_l_name }}</td>
                      <td><a href="mailto:{{ $list->bor_email }}">{{ $list->bor_email }}</a></td>
                      <td><span class="phone-mask">{{ $list->phone }}</span></td>
                      <td>{{ $list->cor_f_name }} {{ $list->cor_l_name }}</td>
                    </tr>
                  @endforeach
                </tbody>
            </table>
            </div>
            <!-- /.card-body -->
                <div class="col-sm-12">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" name="showPrimary" id="showPrimary" class="custom-control-input" {{$checkBoxStatus}} onchange="showPrimary()" />
                        <label class="custom-control-label" for="showPrimary">Only show chapters I am primary for</label>
                    </div>
                </div>
                <div class="card-body text-center">
                        <?php if (Session::get('positionid') >=5 && Session::get('positionid') <=7){ ?>
                        <a class="btn bg-gradient-primary" href="{{ route('chapters.create') }}"><i class="fas fa-plus" ></i>&nbsp;&nbsp;&nbsp;Add New Chapter</a>
                        <?php }?>
                        <?php
                        if($checkBoxStatus){ ?>
                            <button class="btn bg-gradient-primary" disabled><i class="fas fa-download" ></i>&nbsp;&nbsp;&nbsp;Export Chapter List</button>
                        <?php
                        }
                        else{ ?>
                            <a href="{{ route('export.chapter','0') }}"><button class="btn bg-gradient-primary" <?php if($countList ==0) echo "disabled";?>><i class="fas fa-download" ></i>&nbsp;&nbsp;&nbsp;Export Chapter List</button></a>
                        <?php } ?>
                    </div>
                </div>
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
  </section>
  <!-- /.content -->
  @endsection
<!-- /.content-wrapper -->

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
