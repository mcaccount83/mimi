<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
    <a class="dropdown-item" href="{{ route('userreports.useradmin') }}">User Admins</a>
    <a class="dropdown-item" href="{{ route('userreports.duplicateuser') }}">Duplicate Users</a>
    <a class="dropdown-item" href="{{ route('userreports.duplicateboardid') }}">Duplicate Board Details</a>
    <a class="dropdown-item" href="{{ route('userreports.nopresident') }}">Chapters with No President</a>
    <a class="dropdown-item" href="{{ route('userreports.nopresidentinactive') }}">Inactive Chapters with No President</a>
    <a class="dropdown-item" href="{{ route('userreports.noactiveboard') }}">Active Board Members with Inactive User</a>
    <a class="dropdown-item" href="{{ route('userreports.usernoactivecoord') }}">Active User with No Active Coordinator</a>
    <a class="dropdown-item" href="{{ route('userreports.usernoactiveboard') }}">Active User with No Active Board Member</a>
    <a class="dropdown-item" href="{{ route('userreports.outgoingboard') }}">Outgoing Board Members</a>
    <a class="dropdown-item" href="{{ route('userreports.disbandedboard') }}">Disbanded Board Members</a>
</div>
