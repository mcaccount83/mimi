@extends('layouts.coordinator_theme')

@section('page_title', 'Admin Reports')
@section('breadcrumb', 'Conference List')

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
                                    Conference List
                                </h3>
                                @include('layouts.dropdown_menus.menu_reports_admin')
                            </div>
                        </div>
                     <!-- /.card-header -->
        <div class="card-body">
    <table id="chapterlist" class="table table-sm table-hover">
        <thead>
            <tr>
                <th></th>
                <th>Conference</th>
                <th>Regions</th>
                <th>States</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($confList as $list)
            <tr>
                <td>

                </td>
                <td>
                    {{ $list->short_name }}
                </td>
                <td>
                    {{ $list->regions->pluck('long_name')->implode(', ') }}
                </td>
                <td>
                    @if ($list->id == 0)
                        N/A
                    @else
                        {{ $list->states->pluck('state_short_name')->implode(', ') }}
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
             <div class="card-body text-center">
            </div>
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
  </section>
  <!-- /.content -->
@endsection

@push('scripts')
<script>

</script>
@endpush
