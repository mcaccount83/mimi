@extends('layouts.coordinator_theme')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>Chapters</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('coordinators.coorddashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li class="breadcrumb-item active">Active Chapter List</li>
              </ol>
            </div>
          </div>
        </div><!-- /.container-fluid -->
      </section>

     <!-- Main content -->
     <section class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                    <div class="dropdown">
                        <h3 class="card-title dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Active Chapter List
                        </h3>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            @if ($coordinatorCondition)
                                <a class="dropdown-item" href="{{ route('chapters.chaplist') }}">Active Chapter List</a>
                                <a class="dropdown-item" href="{{ route('chapters.chapzapped') }}">Zapped Chapter List</a>
                            @endif
                            @if (($einCondition) || ($adminReportCondition))
                                <a class="dropdown-item" href="{{ route('international.intchapter') }}">International Active Chapter List</a>
                                <a class="dropdown-item" href="{{ route('international.intchapterzapped') }}">International Zapped Chapter List</a>
                            @endif
                        </div>
                    </div>
                </div>
                <!-- /.card-header -->
            <div class="card-body">
                <table id="chapterlist" class="table table-sm table-hover" >
                <thead>
                  <tr>
                    <th>Details</th>
                    <th>Email</th>
                    <th>Conf/Reg</th>
                    <th>State</th>
                    <th>Name</th>
                    <th>EIN</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Primary Coordinator</th>
                  </tr>
                </thead>
                <tbody>
                    @foreach($chapterList as $list)
                        <tr id="chapter-{{ $list->id }}">
                            <td class="text-center align-middle">
                                <a href="{{ url("/chapterdetails/{$list->id}") }}"><i class="fas fa-eye"></i></a>
                            </td>
                            <td class="text-center align-middle">
                                <a href="mailto:" class="email-link" data-chapter="{{ $list->id }}">
                                    <i class="far fa-envelope"></i>
                                </a>
                            </td>
                            <td>
                                @if ($list->reg != "None")
                                    {{ $list->conf }} / {{ $list->reg }}
                                @else
                                    {{ $list->conf }}
                                @endif
                            </td>
                            <td>{{ $list->state }}</td>
                            <td>{{ $list->name }}</td>
                            <td>{{ $list->ein }}</td>
                            <td>{{ $list->bor_f_name }}</td>
                            <td>{{ $list->bor_l_name }}</td>
                            <td>
                                <a href="mailto:{{ $list->bor_email }}">{{ $list->bor_email }}</a>
                            </td>
                            <td><span class="phone-mask">{{ $list->phone }}</span></td>
                            <td>{{ $list->cor_f_name }} {{ $list->cor_l_name }}</td>
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
                <div class="card-body text-center">
                    <?php if($regionalCoordinatorCondition){ ?>
                        <a class="btn bg-gradient-primary" href="{{ route('chapters.chapnew') }}"><i class="fas fa-plus" ></i>&nbsp;&nbsp;&nbsp;Add New Chapter</a>
                        <?php }?>
                        <?php
                        if($checkBoxStatus){ ?>
                            <button class="btn bg-gradient-primary" disabled><i class="fas fa-download" ></i>&nbsp;&nbsp;&nbsp;Export Chapter List</button>
                        <?php
                        }
                        else{ ?>
                            <a href="{{ route('export.chapter','0') }}"><button class="btn bg-gradient-primary" <?php if($countList ==0) echo "disabled";?>><i class="fas fa-download" ></i>&nbsp;&nbsp;&nbsp;Export Chapter List</button></a>
                        <?php } ?>
                    </div>
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
<!-- /.content-wrapper -->

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

    document.addEventListener('DOMContentLoaded', function() {
        // Loop through each chapter and fetch the email details
        document.querySelectorAll('.email-link').forEach(function(emailLink) {
            const chapterId = emailLink.getAttribute('data-chapter');

            fetch('/load-email-details/' + chapterId)
                .then(response => response.json())
                .then(data => {
                    // Build the mailto link with email and cc string
                    const emailListCoord = data.emailListCoord;
                    const emailListChap = data.emailListChap;
                    const subject = 'MOMS Club of ' + data.name + ', ' + data.state;

                    emailLink.setAttribute('href', 'mailto:' + emailListChap + '?cc=' + emailListCoord + '&subject=' + encodeURIComponent(subject));
                })
                .catch(error => {
                    console.error('Error fetching email details:', error);
                });
        });
    });

function showPrimary() {
var base_url = '{{ url("/chapter/chapterlist") }}';

    if ($("#showPrimary").prop("checked") == true) {
        window.location.href = base_url + '?check=yes';
    } else {
        window.location.href = base_url;
    }
}

</script>
@endsection
