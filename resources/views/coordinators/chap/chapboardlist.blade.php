@extends('layouts.mimi_theme')

@section('page_title', 'BoardList')
@section('breadcrumb', 'BoardList Emails')
<style>
    .email-table .email-column {
        max-width: 150px; /* Adjust as needed */
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

</style>

@section('content')
    <!-- Main content -->
     <section class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                  <h3 class="card-title">List of Board Email Addresses</h3>
                </div>
                <!-- /.card-header -->
            <div class="card-body">
              <table id="chapterlist" class="table table-sm table-hover email-table">
              <thead>
			    <tr>
			      <th>Conf/Reg</th>
				  <th>State</th>
                  <th>Name</th>
                  <th>Chapter Email</th>
                  <th>Prez Email</th>
                  <th>AVP Email</th>
                <th>MVP Email</th>
                <th>Sec Email</th>
                <th>Treas Email</th>
                </tr>
                </thead>
                <tbody>
                @foreach($activeChapterList as $list)
                  <tr>
                    <td>
                        @if ($list->state->conference_id > 0)
                            {{ $list->state->conference->short_name }} / {{ $list->state->region->short_name }}
                        @else
                            {{ $list->state->conference->short_name }}
                        @endif
                    </td>
                    <td>
                        @if($list->state_id < 52)
                            {{$list->state->state_short_name}}
                        @else
                            {{$list->state->country?->short_name}}
                        @endif
                    </td>
                    <td>{{ $list->name }}</td>
                    <td class="email-column">@mailto($list->email?? null)</td>
                    <td class="email-column">@mailto($list->president->email)</td>
                    <td class="email-column">@mailto($list->avp->email?? null)</td>
                    <td class="email-column">@mailto($list->mvp->email?? null)</td>
                    <td class="email-column">@mailto($list->secretary->email?? null)</td>
                    <td class="email-column">@mailto($list->treasurer->email?? null)</td>
			      </tr>
                  @endforeach
                  </tbody>
                </table>
            </div>
                <div class="card-body text-center mt-3">

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

