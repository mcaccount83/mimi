@extends('layouts.coordinator_theme')

@section('content')
<section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Chapter Awards Report</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">Chapter Awards Report</li>
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
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Report of Chapter Awards&nbsp;<small>(Chapters that were added after June 30, <?php echo date('Y');?> will not be listed)</small></h3>
                </div>
                <!-- /.card-header -->
            <div class="card-body">
                <table id="chapterlist" class="table table-sm table-hover" >
                <thead>
                  <tr>
                  <th>Add/Edit</th>
                  <th>State</th>
                  <th>Name</th>
                  <th>Award 1</th>
                  <th>Award 2</th>
                  <th>Award 3</th>
                  <th>Award 4</th>
                  <th>Award 5</th>
                  </tr>
                  </thead>
                  <tbody>
                  @foreach($chapterList as $list)
                    <tr>
                        <td class="text-center">
                           <?php if (Session::get('positionid') >=5 && Session::get('positionid') <=7 || $position = 25){ ?>
                                <a href="<?php echo url("/chapter/awardsview/{$list->id}") ?>"><i class="fas fa-edit"></i></a>
                          <?php }?>
                        </td>
                        <td>{{ $list->state }}</td>
                            <td>{{ $list->name }}</td>
                            <td>@if($list->award_1_nomination_type=='1')
                                Outstanding Specific Service Project
                                @elseif($list->award_1_nomination_type=='2')
                                Outstanding Overall Service Program
                                @elseif($list->award_1_nomination_type=='3')
                                Outstanding Children's Activity
                                @elseif($list->award_1_nomination_type=='4')
                                Outstanding Spirit
                                @elseif($list->award_1_nomination_type=='5')
                                Outstanding Chapter
                                @elseif($list->award_1_nomination_type=='6')
                                Outstanding New Chapter
                                @elseif($list->award_1_nomination_type=='7')
                                Other Outstanding Award
                                @else

                                @endif
                                    @if ($list->award_1_approved)
                                        <div style="background-color:#28a745; color: #ffffff;">YES</div>
                                    @else
                                        @if ($list->award_1_nomination_type)
                                            <div style="background-color:#dc3545; color: #ffffff;">NO</div>
                                        @endif
                                @endif</td>
                            <td>@if($list->award_2_nomination_type=='1')
                                Outstanding Specific Service Project
                                @elseif($list->award_2_nomination_type=='2')
                                Outstanding Overall Service Program
                                @elseif($list->award_2_nomination_type=='3')
                                Outstanding Children's Activity
                                @elseif($list->award_2_nomination_type=='4')
                                Outstanding Spirit
                                @elseif($list->award_2_nomination_type=='5')
                                Outstanding Chapter
                                @elseif($list->award_2_nomination_type=='6')
                                Outstanding New Chapter
                                @elseif($list->award_2_nomination_type=='7')
                                Other Outstanding Award
                                @else

                                @endif
                                    @if ($list->award_2_approved)
                                    <div style="background-color:#28a745; color: #ffffff;">YES</div>
                                    @else
                                        @if ($list->award_2_nomination_type)
                                        <div style="background-color:#dc3545; color: #ffffff;">NO</div>
                                        @endif
                                @endif</td>
                            <td>@if($list->award_3_nomination_type=='1')
                                Outstanding Specific Service Project
                                @elseif($list->award_3_nomination_type=='2')
                                Outstanding Overall Service Program
                                @elseif($list->award_3_nomination_type=='3')
                                Outstanding Children's Activity
                                @elseif($list->award_3_nomination_type=='4')
                                Outstanding Spirit
                                @elseif($list->award_3_nomination_type=='5')
                                Outstanding Chapter
                                @elseif($list->award_3_nomination_type=='6')
                                Outstanding New Chapter
                                @elseif($list->award_3_nomination_type=='7')
                                Other Outstanding Award
                                @else

                                @endif
                                    @if ($list->award_3_approved)
                                    <div style="background-color:#28a745; color: #ffffff;">YES</div>
                                    @else
                                        @if ($list->award_3_nomination_type)
                                        <div style="background-color:#dc3545; color: #ffffff;">NO</div>
                                        @endif
                                @endif</td>
                            <td>@if($list->award_4_nomination_type=='1')
                                Outstanding Specific Service Project
                                @elseif($list->award_4_nomination_type=='2')
                                Outstanding Overall Service Program
                                @elseif($list->award_4_nomination_type=='3')
                                Outstanding Children's Activity
                                @elseif($list->award_4_nomination_type=='4')
                                Outstanding Spirit
                                @elseif($list->award_4_nomination_type=='5')
                                Outstanding Chapter
                                @elseif($list->award_4_nomination_type=='6')
                                Outstanding New Chapter
                                @elseif($list->award_4_nomination_type=='7')
                                Other Outstanding Award
                                @else

                                @endif
                                @if ($list->award_4_approved)
                                <div style="background-color:#28a745; color: #ffffff;">YES</div>
                                @else
                                    @if ($list->award_4_nomination_type)
                                    <div style="background-color:#dc3545; color: #ffffff;">NO</div>
                                    @endif
                            @endif</td>
                            <td>@if($list->award_5_nomination_type=='1')
                                Outstanding Specific Service Project
                                @elseif($list->award_5_nomination_type=='2')
                                Outstanding Overall Service Program
                                @elseif($list->award_5_nomination_type=='3')
                                Outstanding Children's Activity
                                @elseif($list->award_5_nomination_type=='4')
                                Outstanding Spirit
                                @elseif($list->award_5_nomination_type=='5')
                                Outstanding Chapter
                                @elseif($list->award_5_nomination_type=='6')
                                Outstanding New Chapter
                                @elseif($list->award_5_nomination_type=='7')
                                Other Outstanding Award
                                @else

                                @endif
                                    @if ($list->award_5_approved)
                                    <div style="background-color:#28a745; color: #ffffff;">YES</div>
                                    @else
                                        @if ($list->award_5_nomination_type)
                                        <div style="background-color:#dc3545; color: #ffffff;">NO</div>
                                        @endif
                                @endif</td>

                    </tr>
                @endforeach

                    </tbody>
                  </table>
                </div>
                <!-- /.card-body -->
                    <div class="col-sm-12">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="showPrimary" id="showPrimary" class="custom-control-input" {{$checkBoxStatus}} onchange="showPrimary()" />
                            <label class="custom-control-label" for="showPrimary">Only show chapters I am Primary Reviewer for</label>
                        </div>
                    </div>
                    <div class="card-body text-center">
                          <a class="btn bg-gradient-primary" href="{{ route('report.addawards') }}"><i class="fas fa-eye" ></i>&nbsp;&nbsp;&nbsp;View All Chapers</a>

			  <?php
			 if($checkBoxStatus){ ?>
				<a href="{{ route('export.chapteraward',$corId) }}"><button class="btn bg-gradient-primary" <?php if($countList ==0) echo "disabled";?>><i class="fas fa-envelope" ></i>&nbsp;&nbsp;&nbsp;Export Award List</button></a>
			<?php
			 }
			 else{ ?>
				<a href="{{ route('export.chapteraward','0') }}"><button class="btn bg-gradient-primary" <?php if($countList ==0) echo "disabled";?>><i class="fas fa-envelope" ></i>&nbsp;&nbsp;&nbsp;Export Award List</button></a>
			 <?php } ?>



             </div>

            </div>

           </div>
        </div>
      </div>
    </section>


@endsection
@section('customscript')
<script>
         function showPrimary() {
    var base_url = '{{ url("/yearreports/chapterawards") }}';

    if ($("#showPrimary").prop("checked") == true) {
        window.location.href = base_url + '?check=yes';
    } else {
        window.location.href = base_url;
    }
}

</script>
@endsection
