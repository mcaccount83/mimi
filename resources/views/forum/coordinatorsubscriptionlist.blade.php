@extends('layouts.coordinator_theme')

@section('page_title', 'List Subscriptions')
@section('breadcrumb', 'Coordinator Subscription List')

@section('content')

@if ($message = Session::get('success'))
      <div class="alert alert-success">
		<button type="button" class="close" data-dismiss="alert">×</button>
         <p>{{ $message }}</p>
      </div>
    @endif
	 @if ($message = Session::get('fail'))
      <div class="alert alert-danger">
		<button type="button" class="close" data-dismiss="alert">×</button>
         <p>{{ $message }}</p>
      </div>
    @endif

    @if ($message = Session::get('info'))
    <div class="alert alert-warning">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <p>{{ $message }}</p>
    </div>
@endif

 <!-- Main content -->
 <section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <div class="dropdown">
                            <h3 class="card-title dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Coordinator Subscription List
                            </h3>
                            @include('layouts.dropdown_menus.menu_forum')
                        </div>
                    </div>
                 <!-- /.card-header -->
    <div class="card-body">
        <table id="chapterlist" class="table table-sm table-hover" >
              {{-- <table id="chapterlist_reRegDate" class="table table-bordered table-hover"> --}}
              <thead>
      			    <tr>
                        <th>Details</th>
                    <th>Conf/Reg</th>
                    <th>Coordinator Name</th>
                    <th>Primary Position</th>
                    <th>Announcements</th>
                    <th>CoordinatorList</th>
                    <th>BoardList</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($coordinatorList as $list)
                  <tr>
                    <td class="text-center align-middle"><a href="{{ url("/coorddetails/{$list->id}") }}"><i class="fas fa-eye"></i></a></td>

                        <td>
                            @if ($list->region?->short_name != "None" )
                                {{ $list->conference->short_name }} / {{ $list->region?->short_name }}
                            @else
                                {{ $list->conference->short_name }}
                            @endif
                        </td>
                        <td>{{ $list->first_name }} {{ $list->last_name }}</td>
                        @if ( $list->on_leave == 1 )
                            <td @if ( $list->on_leave == 1 ) style="background-color: #ffc107;" @endif>ON LEAVE</td>
                        @else
                            <td>{{ $list->displayPosition->long_title }}</td>
                        @endif
                        <td>
                            @php
                                $Subscriptions = $list->user?->categorySubscriptions?->pluck('category_id')->toArray() ?? [];
                            @endphp
                            {{ in_array(1, $Subscriptions) ? 'YES' : 'NO' }}
                        </td>
                        <td>
                            @php
                                $Subscriptions = $list->user?->categorySubscriptions?->pluck('category_id')->toArray() ?? [];
                            @endphp
                            {{ in_array(2, $Subscriptions) ? 'YES' : 'NO' }}
                        </td>
                        <td>
                            @php
                                $Subscriptions = $list->user?->categorySubscriptions?->pluck('category_id')->toArray() ?? [];
                            @endphp
                            {{ in_array(3, $Subscriptions) ? 'YES' : 'NO' }}
                        </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
            </div>
            <div class="col-sm-12">
                <div class="custom-control custom-switch">
                    <input type="checkbox" name="showDirect" id="showDirect" class="custom-control-input" {{$checkBoxStatus}} onchange="showDirect()" />
                    <label class="custom-control-label" for="showDirect">Show only my direct reports</label>
                </div>
            </div>

            </div>

        </div>
      </div>
    </div>
    </section>
@endsection

@section('customscript')
<script>
document.addEventListener("DOMContentLoaded", function() {
    const dropdownItems = document.querySelectorAll(".dropdown-item");
    const currentPath = window.location.pathname;

    dropdownItems.forEach(item => {
        const itemPath = new URL(item.href).pathname;

        if (itemPath == currentPath) {
            item.classList.add("active");
        }
    });
});

function showDirect() {
    var base_url = '{{ url("/forum/coordinatorsubscriptionlist") }}';

    if ($("#showDirect").prop("checked") == true) {
        window.location.href = base_url + '?check=yes';
    } else {
        window.location.href = base_url;
    }
}
</script>
@endsection
