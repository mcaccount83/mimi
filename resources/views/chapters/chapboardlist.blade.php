@extends('layouts.coordinator_theme')

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
                        @if ($list->region->short_name != "None")
                            {{ $list->conference->short_name }} / {{ $list->region->short_name }}
                        @else
                            {{ $list->conference->short_name }}
                        @endif
                    </td>
                    <td>
                        @if($list->state_id < 52)
                            {{$list->state->state_short_name}}
                        @else
                            {{$list->country->short_name}}
                        @endif
                    </td>
                    <td>{{ $list->name }}</td>
                    <td class="email-column"><a href="mailto:{{ $list->email?? null }}">{{ $list->email?? null }}</a></td>
                    <td class="email-column"><a href="mailto:{{ $list->president->email }}">{{ $list->president->email }}</a></td>
                    <td class="email-column"><a href="mailto:{{ $list->avp->email?? null }}">{{ $list->avp->email?? null }}</a></td>
                    <td class="email-column"><a href="mailto:{{ $list->mvp->email?? null }}">{{ $list->mvp->email?? null }}</a></td>
                    <td class="email-column"><a href="mailto:{{ $list->secretary->email?? null }}">{{ $list->secretary->email?? null }}</a></td>
                    <td class="email-column"><a href="mailto:{{ $list->treasurer->email?? null }}">{{ $list->treasurer->email?? null }}</a></td>
			      </tr>
                  @endforeach
                  </tbody>
                </table>
            </div>
                <div class="card-body text-center">
                    {{-- <a href="{{ route('export.boardlist','0') }}"><button class="btn bg-gradient-primary"><i class="fas fa-download" ></i>&nbsp;&nbsp;&nbsp;Export BoardList</button></a> --}}
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

