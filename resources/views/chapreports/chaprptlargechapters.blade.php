@extends('layouts.coordinator_theme')

@section('page_title', 'Chapter Reports')
@section('breadcrumb', 'Large Chapter Report')

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
                            Large Chapter Report
                        </h3>
                        <span class="ms">Includes chapters that have more than 75 Members</span>
                        @include('layouts.dropdown_menus.menu_reports_chap')
                    </div>
                </div>
                <!-- /.card-header -->
            <div class="card-body">
                <table id="chapterlist" class="table table-sm table-hover" >
              {{-- <table id="chapterlist_large" class="table table-bordered table-hover"> --}}
              <thead>
			    <tr>
					<th>Chapter<br>Details</th>
                    <th>Conf/Reg</th>
				  <th>State</th>
                  <th>Name</th>
                 <th>Chapter Size</th>
				 <th>Last Reported</th>

                </tr>
                </thead>
                <tbody>
                @foreach($chapterList as $list)
                  <tr>
                    <td class="text-center align-middle"><a href="{{ url("/chapter/details/{$list->id}") }}"><i class="bi bi-house-fill"></i></a></td>
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
                        <td>{{ $list->payments->rereg_members }}</td>
						<td><span class="date-mask">{{ $list->payments->rereg_date }}</span></td>
					   </tr>
                  @endforeach
                  </tbody>
                </table>
               </div>
            <!-- /.card-body -->

            <div class="card-body">
                 <div class="col-sm-12">
                    <div class="form-check form-switch">
                        <input type="checkbox" name="showPrimary" id="showPrimary" class="form-check-input" {{ $checkBox1Status ? 'checked' : '' }} onchange="showPrimary()" />
                        <label class="form-check-label" for="showPrimary">Only show chapters I am primary for</label>
                    </div>
                </div>
                @if ($ITCondition || $einCondition)
                    <div class="col-sm-12">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="showIntl" id="showIntl" class="form-check-input" {{ $checkBox51Status ? 'checked' : '' }} onchange="showIntl()" />
                            <label class="form-check-label" for="showIntl">Show International Chapters</label>
                        </div>
                    </div>
                @endif
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
