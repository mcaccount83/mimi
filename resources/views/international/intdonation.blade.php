@extends('layouts.coordinator_theme')

@section('page_title', 'Payments/Donations')
@section('breadcrumb', 'International International Donations Report')

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
                        International M2M & Sustaining Donations
                    </h3>
                    @include('layouts.dropdown_menus.menu_payment')
                </div>
            </div>
            <!-- /.card-header -->
        <div class="card-body">
            <table id="chapterlist" class="table table-sm table-hover" >
              <thead>
			    <tr>
				  <th>Conf/Reg</th><th>State</th>
                  <th>Name</th>
                    <th>M2M Fund Donation</th>
                    <th>Donation Date</th>
                    <th>Sustaining Chapter Donation</th>
                    <th>Donation Date</th>
                </tr>
                </thead>
                <tbody>
                @foreach($chapterList as $list)
                <tr>
                            <td>
                                @if ($list->region->short_name != "None")
                                {{ $list->conference->short_name }} / {{ $list->region->short_name }}
                            @else
                                {{ $list->conference->short_name }}
                            @endif
                            </td>
                            <td>{{ $list->state->state_short_name }}</td>
                            <td>{{ $list->name }}</td>
                    <td>${{ $list->m2m_payment }}</td>
                    <td><span class="date-mask">{{ $list->m2m_date }}</span></td>
                    <td>${{ $list->sustaining_donation }}</td>
                    <td><span class="date-mask">{{ $list->sustaining_date }}</span></td>
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
          <!-- /.box -->

    </section>

    <!-- /.content -->

@endsection
@section('customscript')

@endsection
