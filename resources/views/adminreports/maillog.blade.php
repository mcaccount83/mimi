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
                            <h3 class="card-title dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Mail Log
                            </h3>
                            @include('layouts.dropdown_menus.menu_reports_admin')
                        </div>
                    </div>
            <!-- /.card-header -->
    <div class="card-body">

                    <!-- Logs Table -->
                    <table id="chapterlist" class="table table-sm table-hover" >
                        <thead>
                          <tr>
                            <th>View</th>
                            <th>Date</th>
                            <th>Subject</th>
                            <th>To</th>
                          </tr>
                        </thead>
                        <tbody>
                            @foreach ($maillog as $log)
                            <tr>
                                <td class="text-center align-middle">
                                    <a href="#" class="view-email" data-id="{{ $log->id }}"
                                       data-subject="{{ $log->subject }}"
                                       data-date="{{ $log->date }}"
                                       data-from="{{ $log->from }}"
                                       data-to="{{ $log->to }}"
                                       data-cc="{{ $log->cc ?? '' }}"
                                       data-bcc="{{ $log->bcc ?? '' }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                                <td>{{ $log->date }}</td>
                                <td>{{ $log->subject }}</td>
                                <td>To: {{ $log->to }}
                                    @if($log->cc)
                                        <br>cc: {{ $log->cc }}
                                    @endif
                                    @if($log->bcc)
                                        <br>bcc: {{ $log->bcc }}
                                    @endif
                                </td>

                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($ITCondition)
                    <div class="col-sm-12">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="showAdminAll" id="showAdminAll" class="custom-control-input" {{ $checkBox81Status ? 'checked' : '' }} onchange="showAdminAll()" />
                            <label class="custom-control-label" for="showAdminAll">Show All Emails</label>
                        </div>
                    </div>
                @endif
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

  <!-- Email Modal -->
  <div class="modal fade" id="emailModal" tabindex="-1" role="dialog" aria-labelledby="emailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="emailModalLabel">Message Details</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
              <i class="fas fa-spinner fa-spin"></i> Loading...
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn bg-gradient-danger btn-sm" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('customscript')
<script>
$(document).ready(function() {
    // Handle click on view email links
    $('.view-email').on('click', function(e) {
        e.preventDefault();

        var emailId = $(this).data('id');
        var subject = $(this).data('subject');
        var date = $(this).data('date');
        var from = $(this).data('from');
        var to = $(this).data('to');
        var cc = $(this).data('cc');
        var bcc = $(this).data('bcc');

        // Populate header info
        $('#emailDate').text(date);
        $('#emailFrom').text(from);
        $('#emailTo').text(to);
        $('#emailSubject').text(subject);

        // Show/hide cc and bcc if they exist
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

        // Show loading state
        $('#emailBody').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');

        // Open modal
        $('#emailModal').modal('show');

        // Fetch email body
        fetch('{{ url(config('sentemails.routepath').'/body') }}/' + emailId)
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
