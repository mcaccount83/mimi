<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
    <a class="dropdown-item" href="{{ route('adminreports.paymentlist') }}">Payment List</a>
    <a class="dropdown-item" href="{{ route('adminreports.reregdate') }}">Re-Registration Dates</a>
    @if (($userAdmin))
          <a class="dropdown-item" href="{{ route('adminreports.intpaymentlist') }}">International Payments List</a>
        <a class="dropdown-item" href="{{ route('adminreports.intreregdate') }}">International Re-Registration Dates</a>
    @endif
</div>
