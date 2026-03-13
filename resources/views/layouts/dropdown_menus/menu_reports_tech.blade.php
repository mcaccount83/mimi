<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        <a class="dropdown-item" href="{{ route('logs') }}">System Error Logs</a>
        <a class="dropdown-item" href="{{ url(config('queue-monitor.ui.route.prefix')) }}">Mail Queue</a>
        <a class="dropdown-item" href="{{ route('techreports.adminemail') }}">System Email Settings</a>
        <a class="dropdown-item" href="{{ route('techreports.googledrive') }}">Google Drive Settings</a>
        <a class="dropdown-item" href="{{ route('techreports.viewaschapter.active') }}">View Board Pages</a>
        <a class="dropdown-item" href="{{ route('techreports.eoy') }}">End of Year Procedures</a>
        <a class="dropdown-item" href="{{ route('techreports.conferencelist') }}">Conference List</a>
        <a class="dropdown-item" href="{{ route('techreports.regionlist') }}">Region List</a>
        <a class="dropdown-item" href="{{ route('techreports.statelist') }}">State List</a>
  </div>
