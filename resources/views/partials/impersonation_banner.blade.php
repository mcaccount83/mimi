@if($isImpersonating ?? false)
    <div class="alert alert-warning d-flex justify-content-between align-items-center mb-0 rounded-0 py-2">
        <span><i class="bi bi-person-badge me-2"></i>You are viewing as {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</span>
        <a href="{{ route('impersonate.stop') }}" class="btn btn-sm btn-dark">End "View As"</a>
    </div>
@endif
