@extends('layouts.coordinator_theme')

@section('content')
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      Add EOY Awards
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Chapter Awards Report</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
		<div class="col-md-12">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Report of Chapter Awards - All Chapters</h3>
              &nbsp;&nbsp;(Chapters that were added after June 30, <?php echo date('Y');?> will not be listed)

            </div>
            <!-- /.box-header -->

            <div class="box-body table-responsive">
              <table id="chapterlist" class="table table-bordered table-hover">
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
                      <td>
                         <?php if (Session::get('positionid') >=5 && Session::get('positionid') <=7 || $position = 25){ ?>
							<a href="<?php echo url("/chapter/awardsview/{$list->id}") ?>"><i class="fa fa-pencil-square" aria-hidden="true"></i></a>
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
                                    <div style="background-color: #C6EFCE;">YES</div>
                                @else
                                    @if ($list->award_1_nomination_type)
                                        <div style="background-color: #FFC7CE;">NO</div>
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
                                    <div style="background-color: #C6EFCE;">YES</div>
                                @else
                                    @if ($list->award_2_nomination_type)
                                        <div style="background-color: #FFC7CE;">NO</div>
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
                                    <div style="background-color: #C6EFCE;">YES</div>
                                @else
                                    @if ($list->award_3_nomination_type)
                                        <div style="background-color: #FFC7CE;">NO</div>
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
                                    <div style="background-color: #C6EFCE;">YES</div>
                                @else
                                    @if ($list->award_4_nomination_type)
                                        <div style="background-color: #FFC7CE;">NO</div>
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
                                    <div style="background-color: #C6EFCE;">YES</div>
                                @else
                                    @if ($list->award_5_nomination_type)
                                        <div style="background-color: #FFC7CE;">NO</div>
                                    @endif
                            @endif</td>
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
    var base_url = '{{ url("/yearreports/addawards") }}';

    if ($("#showPrimary").prop("checked") == true) {
        window.location.href = base_url + '?check=yes';
    } else {
        window.location.href = base_url;
    }
}

</script>
@endsection
