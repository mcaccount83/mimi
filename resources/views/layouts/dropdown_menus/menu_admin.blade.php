<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
    <a class="dropdown-item" href="{{ route('admin.reregdate') }}">Re-Registration Renewal Dates</a>
      <a class="dropdown-item" href="{{ route('admin.eoy') }}">End of Year Procedures</a>
      <a class="dropdown-item" href="{{ route('admin.duplicateuser') }}">Duplicate Users</a>
      <a class="dropdown-item" href="{{ route('admin.duplicateboardid') }}">Duplicate Board Details</a>
      <a class="dropdown-item" href="{{ route('admin.nopresident') }}">Chapters with No President</a>
      <a class="dropdown-item" href="{{ route('admin.outgoingboard') }}">Outgoing Board Members</a>
      <a class="dropdown-item" href="{{ route('admin.googledrive') }}">Google Drive Settings</a>
      <a class="dropdown-item" href="{{ route('queue-monitor::index') }}">Outgoing Mail Queue</a>
      <a class="dropdown-item" href="{{ url(config('sentemails.routepath')) }}" target="_blank">Sent Mail</a>
      <a class="dropdown-item" href="{{ route('logs') }}" target="_blank">System Error Logs</a>
  </div>
