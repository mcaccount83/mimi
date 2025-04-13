<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
    <a class="dropdown-item" href="{{ route('admin.reregdate') }}">Re-Registration Renewal Dates</a>
      <a class="dropdown-item" href="{{ route('admin.eoy') }}">End of Year Procedures</a>
      <a class="dropdown-item" href="{{ route('admin.chapterlist') }}">Admin Active Board Pages</a>
      <a class="dropdown-item" href="{{ route('admin.chapterlistzapped') }}">Admin Zapped Board Pages</a>
      <a class="dropdown-item" href="{{ route('admin.googledrive') }}">Google Drive Settings</a>
      <a class="dropdown-item" href="{{ url(config('queue-monitor.ui.route.prefix')) }}">Mail Queue</a>
      <a class="dropdown-item" href="{{ url(config('sentemails.routepath')) }}">Sent Mail Log</a>
      <a class="dropdown-item" href="{{ route('payment-logs.index') }}">Payments Log</a>
      <a class="dropdown-item" href="{{ route('logs') }}">System Error Logs</a>
  </div>
