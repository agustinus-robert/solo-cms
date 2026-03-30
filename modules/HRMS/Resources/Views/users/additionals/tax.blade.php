<div class="card mb-4 border-info">
    <div class="card-body p-3">
        <form action="{{ route('hrms::system.users.tax-action', ['user' => $user->id]) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="fw-bold mb-0" style="font-size: 0.9rem;">Status Pajak Karyawan</h6>
                    <p class="text-muted mb-0" style="font-size: 0.75rem;">Aktifkan untuk memproses perhitungan pajak.</p>
                </div>
                <div class="form-check form-switch d-flex align-items-center">
                    <input class="form-check-input" type="checkbox" name="is_tax_active" id="tax_status" value="1" 
                           {{ $user->getMeta('tax_status') ? 'checked' : '' }} 
                           onchange="this.form.submit()" 
                           style="width: 40px; height: 20px; cursor: pointer;">
                    
                    <label class="form-check-label ms-2 fw-bold {{ $user->getMeta('tax_active') ? 'text-success' : 'text-secondary' }}" 
                           for="tax_status" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                        {{ $user->getMeta('tax_status') ? 'aktif' : 'tidak aktif' }}
                    </label>
                </div>
            </div>
        </form>
    </div>
</div>

<p>Apabila pengisian belum dilakukan, mohon untuk mengisi melalui portal masing-masing karyawan.</p>