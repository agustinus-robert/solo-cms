@php
    $isPos = request()->query('pos') === 'true';
    $isOpen = ($cashRegister && $cashRegister->status === 'open');
@endphp

<div class="row g-2 align-items-center">
    <div class="col-6">
        <label class="small fw-bold text-secondary mb-1">Metode</label>
        <select name="payment_type" id="paymentType" class="form-select form-select-sm border-light shadow-none" {{ !$isOpen ? 'disabled' : '' }}>
            <option value="cash">Cash</option>
            <option value="cicilan">Cicilan</option>
            <option value="kasbon">Kasbon</option>
        </select>
    </div>
    <div class="col-6">
        <label class="small fw-bold text-secondary mb-1">Bayar (Rp)</label>
        <input type="number" name="amount_paid" id="amountPaid" class="form-control form-control-sm border-light shadow-none" placeholder="0" {{ !$isOpen ? 'disabled' : '' }}>
    </div>
</div>

<div class="d-flex justify-content-between mt-2 small text-muted border-bottom pb-2">
    <span>Kembali:</span>
    <span class="fw-bold text-success" id="textChange">Rp 0</span>
</div>

<div class="mt-2">
    <div class="d-flex justify-content-between small">
        <span class="text-secondary">Subtotal</span>
        <span id="textSubtotal">Rp 0</span>
    </div>
    <div class="d-flex justify-content-between align-items-center small mt-1">
        <span class="text-secondary">Diskon (Rp)</span>
        <input type="number" id="inputDiscount" name="discount" class="form-control form-control-sm py-0 border-light shadow-none"
               style="width: 80px;" value="{{ $sale->discount ?? 0 }}" {{ !$isOpen ? 'disabled' : '' }}>
    </div>
    <div class="d-flex justify-content-between small mt-1">
        <span class="text-secondary">PPN (11%)</span>
        <span id="textPPN">Rp 0</span>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
    <span class="fw-bold text-dark">Grand Total</span>
    <span class="h4 fw-bold text-primary mb-0" id="textGrandTotal">Rp 0</span>
</div>

<button type="submit" id="btnSubmit" class="btn btn-primary w-100 mt-2 py-2 fw-bold shadow-sm" {{ !$isOpen ? 'disabled' : '' }}>
    {{ $isOpen ? 'SIMPAN TRANSAKSI' : 'BUKA KASIR DAHULU' }}
</button>

@include('poz::transaction.sale.partials.cash-registers')
