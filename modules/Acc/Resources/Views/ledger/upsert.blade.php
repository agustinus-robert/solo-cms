@extends('acc::layouts.default')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold">{{ $ledger ? 'Edit Jurnal' : 'Buat Jurnal Manual' }}</h5>
    </div>
    <form action="{{ $ledger ? route('acc::ledger.update', $ledger->id) : route('acc::ledger.store') }}" method="POST" id="ledgerForm">
        @csrf
        @if($ledger) @method('PUT') @endif

        <div class="card-body">
            <!-- Header Row -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Tanggal Transaksi</label>
                    <input type="date" name="transaction_date" class="form-control" value="{{ old('transaction_date', $ledger->transaction_date ?? date('Y-m-d')) }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">No. Referensi</label>
                    <input type="text" name="reference_number" class="form-control" value="{{ old('reference_number', $ledger->reference_number ?? '') }}" placeholder="Contoh: JV-001" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Deskripsi / Keterangan</label>
                    <input type="text" name="description" class="form-control" value="{{ old('description', $ledger->description ?? '') }}" placeholder="Keterangan transaksi...">
                </div>
            </div>

            <!-- Entries Table -->
            <div class="table-responsive">
                <table class="table table-bordered align-middle" id="ledgerTable">
                    <thead class="table-light">
                        <tr>
                            <th>Akun (COA)</th>
                            <th width="200">Debit</th>
                            <th width="200">Kredit</th>
                            <th width="50"></th>
                        </tr>
                    </thead>
                    <tbody id="entryBody">
                        @if($ledger)
                            @foreach($ledger->entries as $index => $entry)
                                @include('acc::ledger._row', ['index' => $index, 'entry' => $entry])
                            @endforeach
                        @else
                            @include('acc::ledger._row', ['index' => 0])
                            @include('acc::ledger._row', ['index' => 1])
                        @endif
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold bg-light">
                            <td class="text-end">TOTAL</td>
                            <td><input type="text" id="displayTotalDebit" class="form-control-plaintext fw-bold text-success" readonly value="0"></td>
                            <td><input type="text" id="displayTotalCredit" class="form-control-plaintext fw-bold text-danger" readonly value="0"></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addRow()">
                <i class="mdi mdi-plus me-1"></i> Tambah Baris
            </button>

            <!-- Alert Balance -->
            <div id="balanceAlert" class="alert alert-warning mt-3 d-none">
                <i class="mdi mdi-alert me-2"></i> Jurnal belum balance. Selisih: <span id="diffAmount">0</span>
            </div>
        </div>

        <div class="card-footer bg-white py-3 d-flex justify-content-between">
            <a href="{{ route('acc::ledger.index') }}" class="btn btn-light border">Batal</a>
            <button type="submit" id="btnSubmit" class="btn btn-primary px-4">Simpan Jurnal</button>
        </div>
    </form>
</div>

<script>
    let rowIndex = {{ $ledger ? $ledger->entries->count() : 2 }};

    function addRow() {
        const template = ` @include('acc::ledger._row', ['index' => 'REPLACE_INDEX']) `;
        const html = template.replace(/REPLACE_INDEX/g, rowIndex);
        document.getElementById('entryBody').insertAdjacentHTML('beforeend', html);
        rowIndex++;
        calculateTotal();
    }

    function removeRow(btn) {
        if(document.querySelectorAll('#entryBody tr').length > 2) {
            btn.closest('tr').remove();
            calculateTotal();
        } else {
            alert('Minimal harus ada 2 entri (Debit & Kredit)');
        }
    }

    function calculateTotal() {
        let totalDebit = 0;
        let totalCredit = 0;

        document.querySelectorAll('.input-debit').forEach(input => {
            totalDebit += parseFloat(input.value) || 0;
        });

        document.querySelectorAll('.input-credit').forEach(input => {
            totalCredit += parseFloat(input.value) || 0;
        });

        document.getElementById('displayTotalDebit').value = totalDebit.toLocaleString('id-ID');
        document.getElementById('displayTotalCredit').value = totalCredit.toLocaleString('id-ID');

        const diff = Math.abs(totalDebit - totalCredit);
        const alertBox = document.getElementById('balanceAlert');
        const btnSubmit = document.getElementById('btnSubmit');

        if (diff > 0.01) {
            alertBox.classList.remove('d-none');
            document.getElementById('diffAmount').innerText = diff.toLocaleString('id-ID');
            btnSubmit.disabled = true;
        } else {
            alertBox.classList.add('d-none');
            btnSubmit.disabled = false;
        }
    }

    // Event Delegation untuk hitung otomatis saat input berubah
    document.getElementById('entryBody').addEventListener('input', (e) => {
        if(e.target.classList.contains('input-debit') || e.target.classList.contains('input-credit')) {
            // Prevent input di dua kolom pada baris yang sama
            const row = e.target.closest('tr');
            if(e.target.classList.contains('input-debit') && e.target.value > 0) {
                row.querySelector('.input-credit').value = 0;
            } else if(e.target.classList.contains('input-credit') && e.target.value > 0) {
                row.querySelector('.input-debit').value = 0;
            }
            calculateTotal();
        }
    });

    // Jalankan kalkulasi saat page load (untuk Edit)
    window.onload = calculateTotal;
</script>
@endsection
