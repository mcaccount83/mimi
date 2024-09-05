@extends('layouts.coordinator_theme')

@section('content')
<section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>M2M & Sustaning Chapter Donations Report</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">M2M & Sustaning Chapter Donations Report</li>
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
                  <h3 class="card-title">List of Chapters</h3>
                </div>
                <!-- /.card-header -->
            <div class="card-body">
                <table id="chapterlist" class="table table-sm table-hover" >
              {{-- <table id="chapterlist_donation" class="table table-bordered table-hover"> --}}
              <thead>
			    <tr>
					<?php if(Session::get('positionid') >=6 && Session::get('positionid') <=7){ ?><th>Donation</th><?php }?>
				  <th>State</th>
                  <th>Name</th>
                    <th>M2M Fund Donation</th>
                    <th>Donation Date</th>
                    <th>Sustaining Chapter Donation</th>
                    <th>Donation Date</th>
                </tr>
                </thead>
                <tbody>
                @foreach($chapterList as $list)
                  <tr>
	                    <?php if(Session::get('positionid') >=6 && Session::get('positionid') <=7){ ?>
                            <td class="text-center align-middle">
	                            <a href="<?php echo url("/chapter/m2mdonation/{$list->id}") ?>"><i class="far fa-credit-card "></i></a><?php }?></td>
						<td>{{ $list->state }}</td>
                        <td>{{ $list->name }}</td>
						<td>${{ $list->m2m_payment }}</td>
						<td><span class="date-mask">{{ $list->m2m_date }}</span></td>
						<td>${{ $list->sustaining_donation }}</td>
						<td><span class="date-mask">{{ $list->sustaining_date }}</span></td>
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

@endsection
