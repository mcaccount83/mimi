@extends('layouts.coordinator_theme')

@section('page_title', 'Chapter Website/Social Media')
@section('breadcrumb', 'International Social Media List')

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
                        International Social Media List
                    </h3>
                    @include('layouts.dropdown_menus.menu_website')
                </div>
            </div>
            <!-- /.card-header -->
        <div class="card-body">
            <table id="chapterlist" class="table table-sm table-hover" >
              <thead>
			    <tr>
                    <th>Details</th>
                    <th>Conf/Reg>
                  <th>State</th>
                  <th>Name</th>
                    <th>Facebook</th>
                    <th>Twitter</th>
                    <th>Instagram</th>
                </tr>
                </thead>
                <tbody>
                @foreach($chapterList as $list)
                  <tr>
                    <td class="text-center align-middle"><a href="{{ url("/chapterwebsiteedit/{$list->id}") }}"><i class="fas fa-eye "></i></a></td>
                    <td>
                        @if ($list->region->short_name != "None")
                            {{ $list->conference->short_name }} / {{ $list->region->short_name }}
                        @else
                            {{ $list->conference->short_name }}
                        @endif
                    </td>
                    <td>{{ $list->state->state_short_name }}</td>
                        <td>{{ $list->name }}</td>
						<td>{{ $list->social1 }}</td>
						<td>{{ $list->social2 }}</td>
						<td>{{ $list->social3 }}</td>
			        </tr>
                  @endforeach
                  </tbody>
                </table>
                </div>
                <div class="card-body text-center">&nbsp;</div>
            </div>
              </div>
              </div>
            </div>
    </section>
    <!-- /.content -->

@endsection
@section('customscript')
<script>
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
