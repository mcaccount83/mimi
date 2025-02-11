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
                        <h3 class="card-title dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Large Chapter Report
                        </h3>
                        <span class="ml-2">Includes chapters that have more than 75 Members</span>
                        @include('layouts.dropdown_menus.menu_reports_chap')
                    </div>
                </div>
                <!-- /.card-header -->
            <div class="card-body">
                <table id="chapterlist" class="table table-sm table-hover" >
              {{-- <table id="chapterlist_large" class="table table-bordered table-hover"> --}}
              <thead>
			    <tr>
					<th>Details</th>
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
                    <td class="text-center align-middle"><a href="{{ url("/chapterdetails/{$list->id}") }}"><i class="fas fa-eye"></i></a></td>
                    <td>
                        @if ($list->region->short_name != "None")
                            {{ $list->conference->short_name }} / {{ $list->region->short_name }}
                        @else
                            {{ $list->conference->short_name }}
                        @endif
                    </td>
                    <td>{{ $list->state->state_short_name }}</td>
                        <td>{{ $list->name }}</td>
                        <td>{{ $list->members_paid_for }}</td>
						<td><span class="date-mask">{{ $list->dues_last_paid }}</span></td>
					   </tr>
                  @endforeach
                  </tbody>
                </table>
            </div>
                 <!-- /.card-body -->
                 <div class="col-sm-12">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" name="showPrimary" id="showPrimary" class="custom-control-input" {{$checkBoxStatus}} onchange="showPrimary()" />
                        <label class="custom-control-label" for="showPrimary">Only show chapters I am primary for</label>
                    </div>
                </div>
                <div class="card-body text-center">&nbsp;</div>
            </div>
           </div>
          <!-- /.box -->
        </div>
      </div>
    </section>
    <!-- /.content -->
@endsection
@section('customscript')
<script>
function showPrimary() {
    var base_url = '{{ url("/chapterreports/largechapters") }}';

    if ($("#showPrimary").prop("checked") == true) {
        window.location.href = base_url + '?check=yes';
    } else {
        window.location.href = base_url;
    }
}

document.addEventListener("DOMContentLoaded", function() {
    const dropdownItems = document.querySelectorAll(".dropdown-item");
    const currentPath = window.location.pathname;

    dropdownItems.forEach(item => {
        const itemPath = new URL(item.href).pathname;

        if (itemPath === currentPath) {
            item.classList.add("active");
        }
    });
});

</script>
@endsection
