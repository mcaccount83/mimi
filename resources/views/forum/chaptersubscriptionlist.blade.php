@extends('layouts.coordinator_theme')

@section('page_title', 'List Subscriptions')
@section('breadcrumb', 'Chapter Subscription List')

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
                                Chapter Subscription List
                            </h3>
                            <span class="ml-2">PA=Public Announcements | BL=BoardList</span>
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
          			<th>State</th>
                    <th>Chapter</th>
                    <th>President<br>
                        PA|BL</th>
                    <th>AVP<br>
                        PA|BL</th>
                    <th>MVP<br>
                        PA|BL</th>
                    <th>Secretary<br>
                        PA|BL</th>
                    <th>Treasurer<br>
                        PA|BL</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($chapterList as $list)
                  <tr>
                    <td class="text-center align-middle"><a href="{{ url("/chapter/details/{$list->id}") }}"><i class="fas fa-eye"></i></a></td>

                        <td>

                            @if ($list->region?->short_name != "None" )
                                {{ $list->conference->short_name }} / {{ $list->region?->short_name }}
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
                        <td>
                            @php
                                $presSubscriptions = $list->president?->user?->categorySubscriptions?->pluck('category_id')->toArray() ?? [];
                            @endphp
                            {{ in_array(1, $presSubscriptions) ? 'YES' : 'NO' }} |
                            {{ in_array(3, $presSubscriptions) ? 'YES' : 'NO' }}
                        </td>
                        <td>
                            @if($list->avp)
                                @php
                                    $avpSubscriptions = $list->avp?->user?->categorySubscriptions?->pluck('category_id')->toArray() ?? [];
                                @endphp
                                {{ in_array(1, $avpSubscriptions) ? 'YES' : 'NO' }} |
                                {{ in_array(3, $avpSubscriptions) ? 'YES' : 'NO' }}
                            @else
                                &nbsp;
                            @endif
                        </td>
                        <td>
                            @if($list->mvp)
                                @php
                                    $mvpSubscriptions = $list->mvp?->user?->categorySubscriptions?->pluck('category_id')->toArray() ?? [];
                                @endphp
                                {{ in_array(1, $mvpSubscriptions) ? 'YES' : 'NO' }} |
                                {{ in_array(3, $mvpSubscriptions) ? 'YES' : 'NO' }}
                            @else
                                &nbsp;
                            @endif
                        </td>
                        <td>
                            @if($list->secretary)
                                @php
                                    $secSubscriptions = $list->secretary?->user?->categorySubscriptions?->pluck('category_id')->toArray() ?? [];
                                @endphp
                                {{ in_array(1, $secSubscriptions) ? 'YES' : 'NO' }} |
                                {{ in_array(3, $secSubscriptions) ? 'YES' : 'NO' }}
                            @else
                                &nbsp;
                            @endif
                        </td>
                        <td>
                            @if($list->treasurer)
                                @php
                                    $tresSubscriptions = $list->treasurer?->user?->categorySubscriptions?->pluck('category_id')->toArray() ?? [];
                                @endphp
                                {{ in_array(1, $tresSubscriptions) ? 'YES' : 'NO' }} |
                                {{ in_array(3, $tresSubscriptions) ? 'YES' : 'NO' }}
                            @else
                                &nbsp;
                            @endif
                        </td>
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
                @if ($coordinatorCondition && $assistRegionalCoordinatorCondition)
                    <div class="col-sm-12">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="showAllConf" id="showAllConf" class="custom-control-input" {{$checkBox3Status}} onchange="showAllConf()" />
                            <label class="custom-control-label" for="showAllConf">Show All Chapters</label>
                        </div>
                    </div>
                @endif
                @if ($ITCondition)
                    <div class="col-sm-12">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="showAll" id="showAll" class="custom-control-input" {{$checkBox5Status}} onchange="showAll()" />
                            <label class="custom-control-label" for="showAll">Show All International Chapters</label>
                        </div>
                    </div>
                @endif

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

function showPrimary() {
    var base_url = '{{ url("/forum/chaptersubscriptionlist") }}';
    if ($("#showPrimary").prop("checked") == true) {
        window.location.href = base_url + '?{{ \App\Enums\ChapterCheckbox::PRIMARY_COORDINATOR }}=yes';
    } else {
        window.location.href = base_url;
    }
}

function showAllConf() {
    var base_url = '{{ url("/forum/chaptersubscriptionlist") }}';
    if ($("#showAllConf").prop("checked") == true) {
        window.location.href = base_url + '?{{ \App\Enums\ChapterCheckbox::CONFERENCE_REGION }}=yes';
    } else {
        window.location.href = base_url;
    }
}

function showAll() {
    var base_url = '{{ url("/forum/chaptersubscriptionlist") }}';
    if ($("#showAll").prop("checked") == true) {
        window.location.href = base_url + '?{{ \App\Enums\ChapterCheckbox::INTERNATIONAL }}=yes';
    } else {
        window.location.href = base_url;
    }
}
</script>
@endsection
