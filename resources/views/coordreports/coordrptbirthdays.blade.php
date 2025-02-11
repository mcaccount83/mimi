@extends('layouts.coordinator_theme')

@section('page_title', 'Coordinator Reports')
@section('breadcrumb', 'Coordinator Birthday Report')

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
                            Coordinator Birthday Report
                        </h3>
                        @include('layouts.dropdown_menus.menu_reports_coor')
                    </div>
                </div>
                <!-- /.card-header -->
            <div class="card-body">
                <table id="coordinatorlist" class="table table-sm table-hover" >
				<thead>
			    <tr>
			        <th>Details</th>
			        <th>Conf/Reg</th>
					<th>Coordinator Name</th>
					<th>Birthday</th>
					<th>Card Sent</th>
                </tr>
                </thead>
                <tbody>

                @foreach($coordinatorList as $list)
                  <tr>
                      <td class="text-center align-middle">
                        <a href="{{ url("/coorddetails/{$list->cor_id}") }}"><i class="fas fa-eye"></i></a></td>
                        <td>
                            @if ($list->region->short_name != "None")
                                {{ $list->conference->short_name }} / {{ $list->region->short_name }}
                            @else
                                {{ $list->conference->short_name }}
                            @endif
                        </td>
                        <td>{{ $list->first_name }} {{ $list->last_name }}</td>
                    <td data-sort="{{str_pad($list->birthday_month_id, 2, '0', STR_PAD_LEFT) . '-' .  $list->birthday_day}}">{{ $list->birthdayMonth->month_long_name }}  {{ $list->birthday_day }}</td>
                    <td><span class="date-mask">{{ $list->card_sent }}</span></td>
                    </tr>
                  @endforeach
                  </tbody>
                </table>
            </div>
           <!-- /.card-body -->
        </div>
        <div class="card-body text-center">&nbsp;</div>
    </div>
      </div>
    </div>
    </section>
    <!-- Main content -->

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
