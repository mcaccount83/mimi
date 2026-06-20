@extends('layouts.mimi_theme')

@section('page_title', 'IT Reports')
@section('breadcrumb', 'System Error Logs')
  <style>
    /* body {
      padding: 25px;
    } */

    /* h1 {
      font-size: 1.5em;
      margin-top: 0;
    } */

    /* #table-log {
        font-size: 0.85rem;
    } */
    .stack {
      font-size: 0.85em;
    }

    .btn {
        font-size: 0.7rem;
    }

    .date {
      min-width: 75px;
    }

    .text {
      word-break: break-all;
    }

    a.llv-active {
      z-index: 2;
      background-color: #f5f5f5;
      border-color: #777;
    }

    .list-group-item {
      word-break: break-word;
    }

    .folder {
      padding-top: 15px;
    }

    .div-scroll {
      height: 80vh;
      overflow: hidden auto;
    }

    .nowrap {
      white-space: nowrap;
    }

    .list-group {
      padding: 5px;
    }

  </style>

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid table-container">
            <div class="row">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <div class="dropdown">
                            <h3 class="card-title dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                System Error Logs
                            </h3>
                            @include('layouts.dropdown_menus.menu_reports_tech')
                        </div>
                    </div>
            <!-- /.card-header -->
    <!-- /.card-header -->
    <div class="card-body">

      @if ($logs === null)
        <div>
          Log file >50M, please download it.
        </div>
      @else

        <table id="table-log" class="table table-striped" data-ordering-index="{{ $standardFormat ? 2 : 0 }}">
          <thead>
          <tr>
            @if ($standardFormat)
              <th>Level</th>
              <th>Context</th>
              <th>Date</th>
            @else
              <th>Line number</th>
            @endif
            <th>Content</th>
          </tr>
          </thead>
          <tbody>
          @foreach($logs as $key => $log)
            <tr data-display="stack{{{$key}}}">
              @if ($standardFormat)
                <td class="nowrap text-{{{$log['level_class']}}}">
                  <span class="fa fa-{{{$log['level_img']}}}" aria-hidden="true"></span>&nbsp;&nbsp;{{$log['level']}}
                </td>
                <td class="text">{{$log['context']}}</td>
              @endif
              <td class="date">{{{$log['date']}}}</td>
              <td class="text">
                @if ($log['stack'])
                  <button type="button"
                          class="float-right expand btn btn-outline-dark btn-sm mb-2 ml-2"
                          data-display="stack{{{$key}}}">
                    <span class="fa fa-search"></span>
                  </button>
                @endif
                {{{$log['text']}}}
                @isset($log['in_file'])
                  <br/>{{{$log['in_file']}}}
                @endisset
                @if ($log['stack'])
                  <div class="stack" id="stack{{{$key}}}"
                       style="display: none; white-space: pre-wrap;">{{{ trim($log['stack']) }}}
                  </div>
                @endif
              </td>
            </tr>
          @endforeach
          </tbody>
        </table>
      @endif

      <div class="p-3">
        @if($current_file)
          <a href="?dl={{ \Illuminate\Support\Facades\Crypt::encryptString($current_file) }}{{ ($current_folder) ? '&f=' . \Illuminate\Support\Facades\Crypt::encryptString($current_folder) : '' }}">
            <span class="fa fa-download"></span> Download file
          </a>
          -
          <a id="clean-log" href="#" data-href="?clean={{ \Illuminate\Support\Facades\Crypt::encryptString($current_file) }}{{ ($current_folder) ? '&f=' . \Illuminate\Support\Facades\Crypt::encryptString($current_folder) : '' }}">
    <span class="fa fa-sync"></span> Clean file
</a>
          -
          <a id="delete-log" href="#" data-href="?del={{ \Illuminate\Support\Facades\Crypt::encryptString($current_file) }}{{ ($current_folder) ? '&f=' . \Illuminate\Support\Facades\Crypt::encryptString($current_folder) : '' }}">
    <span class="fa fa-trash"></span> Delete file
</a>
          @if(count($files) > 1)
            -
            <a id="delete-all-log" href="#" data-href="?delall=true{{ ($current_folder) ? '&f=' . \Illuminate\Support\Facades\Crypt::encryptString($current_folder) : '' }}">
    <span class="fa fa-trash-alt"></span> Delete all files
</a>
          @endif
        @endif
      </div>
    </div>

    </div>
</div>
<!-- /.box -->
</div>
</div>
</section>
<!-- Main content -->

<!-- /.content -->
@endsection
@section('customscript')
<script>
$(document).ready(function () {
    $('.table-container tr').on('click', function () {
        $('#' + $(this).data('display')).toggle();
    });

    $('#table-log').DataTable({
        "order": [[$('#table-log').data('ordering-index'), 'desc']],
        "stateSave": true,
        "stateSaveCallback": function (settings, data) {
            window.localStorage.setItem("datatable", JSON.stringify(data));
        },
        "stateLoadCallback": function (settings) {
            var data = JSON.parse(window.localStorage.getItem("datatable"));
            if (data) data.start = 0;
            return data;
        }
    });

    $('#delete-log, #clean-log, #delete-all-log').click(function (e) {
        e.preventDefault();
        const href = $(this).data('href');  // was attr('href'), now data('href')
        Swal.fire({
            title: 'Are you sure?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'Cancel',
            customClass: {
                confirmButton: 'btn btn-sm btn-success',
                cancelButton: 'btn btn-sm btn-danger'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = href;
            }
        });
    });
});
</script>
@endsection
