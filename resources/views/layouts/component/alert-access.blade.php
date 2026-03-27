@if(session('error_access'))
<div class="alert alert-danger alert-dismissible fade show px-4 mb-4" role="alert">
    <div class="d-flex align-items-center">
        <i class="bx bx-error-circle font-size-24 me-3"></i>
        <div>
            <h5 class="alert-heading font-size-16 mb-1">Akses Ditolak!</h5>
            <p class="mb-0">{{ session('error_access') }}</p>
        </div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
