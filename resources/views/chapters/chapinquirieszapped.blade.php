@extends('layouts.coordinator_theme')

@section('page_title', 'Chapters')
@section('breadcrumb', 'Zapped Inquiries Chapter List')
<style>
    .hidden-column {
        display: none !important;
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
                    <div class="dropdown">
                        <h3 class="card-title dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Inquiries Zapped Chapter List
                        </h3>
                        @include('layouts.dropdown_menus.menu_inquiries')
                    </div>
                </div>
                <!-- /.card-header -->
            <div class="card-body">
              <table id="chapterlist" class="table table-sm table-hover">
              <thead>
			    <tr>
					<th>Details</th>
                    <th>Conf/Reg</th>
                    <th>State</th>
                    <th>Chapter Name</th>
                    <th>Boundaries</th>
                    <th>Inquiries Notes</th>
                    <th>Inquiries Email</th>
                </tr>
                </thead>
                <tbody>
                @foreach($chapterList as $list)
                  <tr>
                    <td class="text-center "><a href="{{ url("/chapter/details/{$list->id}") }}"><i class="bi bi-eye"></i></a></td>
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
                    <td>{{ $list->territory }}</td>
                    <td>{{ $list->inquiries_note }}</td>
                        <td>{{ $list->inquiries_contact }}</td>
                  @endforeach
                  </tbody>
                </table>
          </div>
            <!-- /.card-body -->

            <div class="card-body">
              @if ($ITCondition)
                <div class="col-sm-12">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="showIntl" id="showIntl" class="form-check-input" {{ $checkBox51Status ? 'checked' : '' }} onchange="showIntl()" />
                            <label class="form-check-label" for="showIntl">Show All International Chapters</label>
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
