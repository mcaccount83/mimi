@extends('layouts.coordinator_theme')

@section('page_title', 'IT Reports')
@section('breadcrumb', 'Conferences & Regions')

@section('content')
<!-- Main content -->
  <section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <div class="dropdown">
                        <h3 class="card-title dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Conferences & Regions
                        </h3>
                        @include('layouts.dropdown_menus.menu_reports_tech')
                    </div>
                </div>
                 <!-- /.card-header -->
<meta name="csrf-token" content="{{ csrf_token() }}">

      <div class="card-body">
            <table id="chapterlist"  class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th>Conference</th>
                        <th>Description</th>
                        <th>Regions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($confList as $conference)
                        <tr id="conference-{{ $conference->id }}">
                            <td>{{ $conference->conference_name }}</td>
                            <td>{{ $conference->conference_description }} / {{ $conference->short_description }}</td>
                            <td>
                                @if($conference->regions->count() > 0)
                                    <table class="table table-sm table-borderless mb-0">
                                        @foreach($conference->regions as $region)
                                            <tr>
                                                <td>{{ $region->long_name }} / {{ $region->short_name }}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                @else
                                    <em>No regions</em>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

                   <!-- /.card-body -->
                <div class="card-body text-center">
                    @if ($userAdmin)
                        <a class="btn bg-gradient-primary" href="{{ route('chapters.chaplistpending') }}"><i class="fas fa-edit mr-2" ></i>Add/Update Conference</a>
                        <a class="btn bg-gradient-primary" href="{{ route('chapters.chaplistpending') }}"><i class="fas fa-edit mr-2" ></i>Add/Update Region</a>
                    @endif
                    </div>

                </div>
            </div>

            </div>
        </div>
    </div>
</div>
</section>
<!-- /.content -->

@endsection
