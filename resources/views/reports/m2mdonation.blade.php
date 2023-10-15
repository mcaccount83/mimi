@extends('layouts.coordinator_theme')

@section('content')
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      M2M & Sustaining Chapter Donations Report
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">M2M & Sustaining Chapter Donations</li>
      </ol>
    </section>
    	 @if ($message = Session::get('success'))
      <div class="alert alert-success">
         <p>{{ $message }}</p>
      </div>
    @endif
     @if ($message = Session::get('fail'))
      <div class="alert alert-danger">
         <p>{{ $message }}</p>
      </div>
    @endif
    <!-- Main content -->
    <section class="content">
      <div class="row">
		<div class="col-md-12">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">M2M & Sustaining Chapter Donations</h3>
              
            </div>
            <!-- /.box-header -->
            
            <div class="box-body table-responsive">
              <table id="chapterlist_zapped" class="table table-bordered table-hover">
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
	                    <?php if(Session::get('positionid') >=6 && Session::get('positionid') <=7){ ?><td>
	                    <a href="<?php echo url("/chapter/m2mdonation/{$list->id}") ?>"><i class="fa fa-credit-card" aria-hidden="true"></i></a><?php }?>
	                    </td>
						<td>{{ $list->state }}</td>
                        <td>{{ $list->name }}</td>
						<td>${{ $list->m2m_payment }}</td>
						<td>{{ $list->m2m_date }}</td>
						<td>${{ $list->sustaining_donation }}</td>
						<td>{{ $list->sustaining_date }}</td>
			        </tr>
                  @endforeach
                  </tbody>
                </table>

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