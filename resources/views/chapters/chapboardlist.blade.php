@extends('layouts.coordinator_theme')

@section('page_title', 'BoardList')
@section('breadcrumb', 'BoardList Emails')

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
              <table id="chapterlist" class="table table-sm table-hover">
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
                        @if ($list->reg != "None")
                            {{ $list->conf }} / {{ $list->reg }}
                        @else
                            {{ $list->conf }}
                        @endif
                    </td>
                    <td>{{ $list->state }}</td>
                    <td>{{ $list->name }}</td>
                    <td>{{ $list->chapter_email }}</td>
                    <td>{{ $list->pre_email }}</td>
                    <td>{{ $list->avp_email }}</td>
                    <td>{{ $list->mvp_email }}</td>
                    <td>{{ $list->sec_email }}</td>
                    <td>{{ $list->trs_email }}</td>
			      </tr>
                  @endforeach
                  </tbody>
                </table>
            </div>
                <div class="card-body text-center">
                    <a href="{{ route('export.boardlist','0') }}"><button class="btn bg-gradient-primary"><i class="fas fa-download" ></i>&nbsp;&nbsp;&nbsp;Export BoardList</button></a>
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

</script>
@endsection
