<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
    {{-- <a class="dropdown-item" href="{{ route('techreports.reregdate') }}">Re-Registration Renewal Dates</a> --}}
        <a class="dropdown-item" href="{{ route('logs') }}">System Error Logs</a>
        <a class="dropdown-item" href="{{ url(config('sentemails.routepath')) }}">Sent Mail Log</a>
        <a class="dropdown-item" href="{{ url(config('queue-monitor.ui.route.prefix')) }}">Mail Queue</a>
        <a class="dropdown-item" href="{{ route('techreports.adminemail') }}">System Email Settings</a>
        <a class="dropdown-item" href="{{ route('techreports.googledrive') }}">Google Drive Settings</a>
        <a class="dropdown-item" href="{{ route('techreports.chapterlist') }}">Active Board Pages (View As)</a>
        <a class="dropdown-item" href="{{ route('techreports.chapterlistzapped') }}">Zapped Board Pages (View As)</a>
        <a class="dropdown-item" href="{{ route('techreports.eoy') }}">End of Year Procedures</a>
  </div>
