@php
    $isOpen = ($cashRegister && $cashRegister->status === 'open');
@endphp

<div class="row g-2 align-items-center">
    <div class="col-12" id="wrapperSaldoKasir">
        @if(!$isOpen)
            <div class="alert alert-warning py-2 px-3 mb-0 border-0 d-flex justify-content-between align-items-center" style="background: #fff3cd; border-radius: 8px;">
                <div>
                    <small class="text-secondary d-block" style="font-size: 0.7rem; text-uppercase; font-weight: bold;">Status Kasir:</small>
                    <span class="fw-bold text-danger" style="font-size: 0.9rem;"> <i class="fa fa-lock me-1"></i> CLOSED</span>
                </div>
                <button type="button" class="btn btn-sm btn-success fw-bold px-3 shadow-sm" onclick="handleToggleRegister('open')">
                    <i class="fa fa-door-open me-1"></i> BUKA KASIR
                </button>
            </div>
        @else
            <div class="alert alert-info py-2 px-3 mb-0 border-0 d-flex justify-content-between align-items-center" style="background: #e7f3ff; border-radius: 8px;">
                <div>
                    <small class="text-secondary d-block" style="font-size: 0.7rem; text-uppercase; font-weight: bold;">Saldo Kasir Aktif:</small>
                    <span class="fw-bold text-primary" id="textSaldoKasir" style="font-size: 1.1rem;">
                        Rp {{ number_format($cashRegister->money ?? 0, 0, ',', '.') }}
                    </span>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-primary rounded-circle p-0 d-flex align-items-center justify-content-center shadow-sm"
                            style="width: 32px; height: 32px;" data-bs-toggle="modal" data-bs-target="#modalUpdateCash" title="Adjustment Saldo">
                        <i class="fa fa-plus" style="font-size: 0.9rem;"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger rounded-circle p-0 d-flex align-items-center justify-content-center"
                            style="width: 32px; height: 32px;" onclick="handleToggleRegister('close')" title="Tutup Sesi Kasir">
                        <i class="fa fa-power-off" style="font-size: 0.9rem;"></i>
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>

<div class="modal fade" id="modalUpdateCash" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white py-2">
                <h6 class="modal-title small">Update Saldo Kasir</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="small fw-bold text-secondary">Tipe Perubahan</label>
                    <select id="cash_reg_type" class="form-select form-select-sm border-light shadow-none">
                        <option value="plus">Uang Masuk (Plus)</option>
                        <option value="minus">Uang Keluar (Minus)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="small fw-bold text-secondary text-uppercase" style="font-size: 0.65rem;">Nominal (Rp)</label>
                    <input type="number" id="cash_reg_amount" class="form-control form-control-sm border-light shadow-none fw-bold text-primary" placeholder="0">
                </div>
                <div class="mb-0">
                    <label class="small fw-bold text-secondary text-uppercase" style="font-size: 0.65rem;">Alasan / Note</label>
                    <textarea id="cash_reg_reason" class="form-control form-control-sm border-light shadow-none" rows="2" placeholder="Contoh: Modal awal receh atau Salah kembalian"></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" id="btnSubmitCash" onclick="handleUpdateCash()" class="btn btn-primary btn-sm w-100 fw-bold">SIMPAN PERUBAHAN</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    /**
     * Handle Buka/Tutup Kasir
     */
    function handleToggleRegister(action) {
        const msg = action === 'open' ? "Buka sesi kasir?" : "Tutup sesi kasir?";
        if (!confirm(msg)) return;

        const url = action === 'open'
            ? "{{ route('poz::transaction.cash-registers.open') }}"
            : "{{ route('poz::transaction.cash-registers.close') }}";

        const csrfToken = document.querySelector('input[name="_token"]').value;

        fetch(url, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload(); // Reload untuk ubah state UI
            } else {
                alert(data.message);
            }
        })
        .catch(err => alert("Terjadi kesalahan server."));
    }

    /**
     * Handle Update Saldo (Top Up / Adjustment)
     */
    function handleUpdateCash() {
        const btn = document.getElementById('btnSubmitCash');
        const amountInput = document.getElementById('cash_reg_amount');
        const typeInput = document.getElementById('cash_reg_type');
        const reasonInput = document.getElementById('cash_reg_reason');

        const url = "{{ route('poz::transaction.cash-registers.update') }}";
        const csrfToken = document.querySelector('input[name="_token"]').value;

        if (!amountInput.value || amountInput.value <= 0) {
            alert('Masukkan nominal yang valid!');
            return;
        }

        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Memproses...';

        // Konversi nominal ke plus/minus sebelum kirim ke controller Robert
        let amount = parseFloat(amountInput.value);
        if (typeInput.value === 'minus') amount = -Math.abs(amount);

        const formData = new FormData();
        formData.append('amount', amount);
        formData.append('log_type', 'adjustment'); // Log type untuk manual update
        formData.append('reason', reasonInput.value || 'Update manual kasir');
        formData.append('_token', csrfToken);

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const elSaldo = document.getElementById('textSaldoKasir');
                if (elSaldo) elSaldo.innerText = data.new_balance;

                const modalEl = document.getElementById('modalUpdateCash');
                bootstrap.Modal.getOrCreateInstance(modalEl).hide();

                alert(data.message);
                amountInput.value = '';
                reasonInput.value = '';
            } else {
                alert('Gagal: ' + data.message);
            }
        })
        .catch(err => alert('Gagal menghubungi server.'))
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    }
</script>
@endpush
