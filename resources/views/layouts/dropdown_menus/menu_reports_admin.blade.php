<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
    <a class="dropdown-item" href="{{ route('adminreports.useradmin') }}">User Admins</a>
      <a class="dropdown-item" href="{{ route('adminreports.duplicateuser') }}">Duplicate Users</a>
      <a class="dropdown-item" href="{{ route('adminreports.duplicateboardid') }}">Duplicate Board Details</a>
      <a class="dropdown-item" href="{{ route('adminreports.nopresident') }}">Chapters with No President</a>
      <a class="dropdown-item" href="{{ route('adminreports.outgoingboard') }}">Outgoing Board Members</a>
      <a class="dropdown-item" href="{{ route('adminreports.disbandedboard') }}">Disbanded Board Members</a>
      <a class="dropdown-item" href="{{ url(config('queue-monitor.ui.route.prefix')) }}">Mail Queue</a>
      <a class="dropdown-item" href="{{ url(config('sentemails.routepath')) }}">Sent Mail Log</a>
      <a class="dropdown-item" href="{{ route('payment-logs.index') }}">Payments Log</a>
      <a class="dropdown-item" href="{{ route('logs') }}">System Error Logs</a>
  </div>
