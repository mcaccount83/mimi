<!-- resources/views/adminreports/maillog.blade.php -->
@extends('layouts.coordinator_theme')

@section('page_title', 'Admin Reports')
@section('breadcrumb', 'Mail Log')

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
                                Mail Log
                            </h3>
                            @include('layouts.dropdown_menus.menu_reports_admin')
                        </div>
                    </div>
                    <!-- /.card-header -->

                    <div class="card-body">
                        <!-- Nav Tabs -->
                        <ul class="nav nav-pills mb-3">
                            <li class="nav-item"><a class="nav-link active" href="#to" data-bs-toggle="tab">INBOX (TO)</a></li>
                            <li class="nav-item"><a class="nav-link" href="#cc" data-bs-toggle="tab">INBOX (CC/BCC)</a></li>
                            <li class="nav-item"><a class="nav-link" href="#from" data-bs-toggle="tab">SENT (FROM)</a></li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content">
                            <!-- TO Tab -->
                            <div class="active tab-pane" id="to">
                                <table id="toTable" class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>View</th>
                                            <th>Date</th>
                                            <th>Subject</th>
                                            <th>From</th>
                                            <th>To</th>
                                            <th>cc/bcc</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($toMaillog as $log)
                                        <tr>
                                            <td class="text-center align-middle">
                                                <a href="#" class="view-email" data-id="{{ $log->id }}"
                                                   data-subject="{{ $log->subject }}"
                                                   data-date="{{ $log->date }}"
                                                   data-from="{{ $log->from }}"
                                                   data-to="{{ $log->to }}"
                                                   data-cc="{{ $log->cc ?? '' }}"
                                                   data-bcc="{{ $log->bcc ?? '' }}">
                                                    <i class="bi bi-eye-fill"></i>
                                                </a>
                                            </td>
                                            <td>{{ $log->date }}</td>
                                            <td>{{ $log->subject }}</td>
                                            <td>{{ $log->from }}</td>
                                            <td>{!! str_replace(',', '<br>', $log->to) !!}</td>
                                            <td>
                                                @if($log->cc)
                                                    cc: {!! str_replace(',', '<br>', $log->cc) !!}
                                                @endif
                                                @if ($log->cc && $log->bcc)
                                                    <br>
                                                @endif
                                                @if($log->bcc)
                                                    bcc: {!! str_replace(',', '<br>', $log->bcc) !!}
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- CC Tab -->
                            <div class="tab-pane" id="cc">
                                <table id="ccTable" class="table table-sm table-hover">
                                      <thead>
                                        <tr>
                                            <th>View</th>
                                            <th>Date</th>
                                            <th>Subject</th>
                                            <th>From</th>
                                            <th>To</th>
                                            <th>cc/bcc</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($ccMaillog as $log)
                                        <tr>
                                            <td class="text-center align-middle">
                                                <a href="#" class="view-email" data-id="{{ $log->id }}"
                                                   data-subject="{{ $log->subject }}"
                                                   data-date="{{ $log->date }}"
                                                   data-from="{{ $log->from }}"
                                                   data-to="{{ $log->to }}"
                                                   data-cc="{{ $log->cc ?? '' }}"
                                                   data-bcc="{{ $log->bcc ?? '' }}">
                                                    <i class="bi bi-eye-fill"></i>
                                                </a>
                                            </td>
                                            <td>{{ $log->date }}</td>
                                            <td>{{ $log->subject }}</td>
                                            <td>{{ $log->from }}</td>
                                            <td>{!! str_replace(',', '<br>', $log->to) !!}</td>
                                            <td>
                                                @if($log->cc)
                                                    cc: {!! str_replace(',', '<br>', $log->cc) !!}
                                                @endif
                                                @if ($log->cc && $log->bcc)
                                                    <br>
                                                @endif
                                                @if($log->bcc)
                                                    bcc: {!! str_replace(',', '<br>', $log->bcc) !!}
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- FROM Tab -->
                            <div class="tab-pane" id="from">
                                <table id="fromTable" class="table table-sm table-hover">
                                      <thead>
                                        <tr>
                                            <th>View</th>
                                            <th>Date</th>
                                            <th>Subject</th>
                                            <th>From</th>
                                            <th>To</th>
                                            <th>cc/bcc</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($fromMaillog as $log)
                                        <tr>
                                            <td class="text-center align-middle">
                                                <a href="#" class="view-email" data-id="{{ $log->id }}"
                                                   data-subject="{{ $log->subject }}"
                                                   data-date="{{ $log->date }}"
                                                   data-from="{{ $log->from }}"
                                                   data-to="{{ $log->to }}"
                                                   data-cc="{{ $log->cc ?? '' }}"
                                                   data-bcc="{{ $log->bcc ?? '' }}">
                                                    <i class="bi bi-eye-fill"></i>
                                                </a>
                                            </td>
                                            <td>{{ $log->date }}</td>
                                            <td>{{ $log->subject }}</td>
                                            <td>{{ $log->from }}</td>
                                            <td>{!! str_replace(',', '<br>', $log->to) !!}</td>
                                           <td>
                                                @if($log->cc)
                                                    cc: {!! str_replace(',', '<br>', $log->cc) !!}
                                                @endif
                                                @if ($log->cc && $log->bcc)
                                                    <br>
                                                @endif
                                                @if($log->bcc)
                                                    bcc: {!! str_replace(',', '<br>', $log->bcc) !!}
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="card-body">
                           @if ($listAdminCondition || $ITCondition)
                    <div class="col-sm-12">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="showListAdmin" id="showListAdmin" class="form-check-input" {{ $checkBox5Status ? 'checked' : '' }} onchange="showListAdmin()" />
                            <label class="form-check-label" for="showListAdmin">Show ListAdmin Emails</label>
                        </div>
                    </div>
                @endif
                @if(($coordinatorCondition && $conferenceCoordinatorCondition) || $inquiriesCondition || $inquiriesInternationalCondition || $ITCondition)
                <div class="col-sm-12">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="showInquiries" id="showInquiries" class="form-check-input" {{ $checkBox7Status ? 'checked' : '' }} onchange="showInquiries()" />
                            <label class="form-check-label" for="showInquiries">Show Inquiry Emails</label>
                        </div>
                    </div>
                @endif
                @if ($ITCondition)
                     <div class="col-sm-12">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="showIntlInquiries" id="showIntlInquiries" class="form-check-input" {{ $checkBox57Status ? 'checked' : '' }} onchange="showIntlInquiries()" />
                            <label class="form-check-label" for="showIntlInquiries">Show All Inquiry Emails</label>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="showAdminAll" id="showAdminAll" class="form-check-input" {{ $checkBox81Status ? 'checked' : '' }} onchange="showAdminAll()" />
                            <label class="form-check-label" for="showAdminAll">Show All Emails</label>
                        </div>
                    </div>
                @endif
                  </div>
            <!-- /.card-body for checkboxes -->

             <div class="card-body text-center mt-3">
                 </div>
            <!-- /.card-body for buttons -->

        </div>
        <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </div>
  <!-- /.container-fluid -->
</section>

  <!-- Email Modal -->
  <div class="modal fade" id="emailModal" tabindex="-1" role="dialog" aria-labelledby="emailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="emailModalLabel">Message Details</h4>
          <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div id="emailHeader">
            <p><strong><span id="emailSubject"></span></strong><br>
            Date: <span id="emailDate"></span><br>
            From: <span id="emailFrom"></span><br>
            To: <span id="emailTo"></span><br>
            <span id="emailCcWrapper" style="display:none;">CC: <span id="emailCc"></span></span><br>
            <span id="emailBccWrapper" style="display:none;">BCC: <span id="emailBcc"></span></span></p>
          </div>
          <hr>
          <div id="emailBody">
            <div class="text-center">
              <i class="bi bi-arrow-repeat spin"></i> Loading...
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger bg-gradient btn-sm mb-2" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('customscript')
<script>
var emailBodyUrl = '{{ url(config('sentemails.routepath').'/body') }}';

$(document).ready(function() {
    // Initialize the first visible table
    if (!$.fn.DataTable.isDataTable('#toTable')) {
        $('#toTable').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "order": []
        });
    }

    // Initialize tables when their tab is shown
    $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        var target = $(e.target).attr("href");

        if (target === '#cc' && !$.fn.DataTable.isDataTable('#ccTable')) {
            $('#ccTable').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "order": []
            });
        }

        if (target === '#from' && !$.fn.DataTable.isDataTable('#fromTable')) {
            $('#fromTable').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "order": []
            });
        }

        // Force column width recalculation and redraw
        setTimeout(function() {
            $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust().draw();
        }, 10);
    });

    // Modal handler
    $(document).on('click', '.view-email', function(e) {
        e.preventDefault();

        var emailId = $(this).data('id');
        var subject = $(this).data('subject');
        var date = $(this).data('date');
        var from = $(this).data('from');
        var to = $(this).data('to');
        var cc = $(this).data('cc');
        var bcc = $(this).data('bcc');

        $('#emailDate').text(date);
        $('#emailFrom').text(from);
        $('#emailTo').text(to);
        $('#emailSubject').text(subject);

        if (cc) {
            $('#emailCc').text(cc);
            $('#emailCcWrapper').show();
        } else {
            $('#emailCcWrapper').hide();
        }

        if (bcc) {
            $('#emailBcc').text(bcc);
            $('#emailBccWrapper').show();
        } else {
            $('#emailBccWrapper').hide();
        }

        $('#emailBody').html('<div class="text-center"><i class="bi bi-arrow-repeat spin"></i>Loading...</div>');
        $('#emailModal').modal('show');

        fetch(emailBodyUrl + '/' + emailId)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(html => {
                $('#emailBody').html(html);
            })
            .catch(error => {
                console.error('Error:', error);
                $('#emailBody').html('<div class="alert alert-danger">Failed to load email content. Please try again.</div>');
            });
    });
});
</script>
@endsection
