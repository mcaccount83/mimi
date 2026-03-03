@extends('layouts.mimi_theme')

@section('page_title', 'User Reports')
@section('breadcrumb', 'Chapters with No President')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <div class="dropdown">
                                <h3 class="card-title dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Chapters with No President
                                </h3>
                                @include('layouts.dropdown_menus.menu_reports_user')
                            </div>
                        </div>
                     <!-- /.card-header -->
        <div class="card-body">
            <table id="chapterlist" class="table table-sm table-hover" >
              <thead>
			    <tr>
                    <th>Add<br>President</th>
                    <th>Conf/Reg</th>
                    <th>State</th>
                    <th>Name</th>
                    <th>Chapter ID</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                @foreach($ChapterPres as $list)
                  <tr>
                        <td class="text-center align-middle"><a href="{{ url("/userreports/addnewboard/{$list->id}") }}"><i class="bi bi-person-fill-add"></i></a></td>
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
                        <td>{{ $list->id }}</td>
                        <td>
                            @if($list->active_status == 0)
                                Disbanded
                            @elseif($list->active_status == 1)
                                Active
                            @endif
                        </td>
			        </tr>
                  @endforeach
                  </tbody>
                </table>
         </div>
              <!-- /.card-body -->

              <div class="card-body">
            </div>
            <!-- /.card-body for checkboxes -->

                <div class="card-body text-center mt-3">
            </div>
            <!-- /.card-body for buttons -->

         </div>
        <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </div>
  <!-- /.container-fluid -->
</section>
@endsection
