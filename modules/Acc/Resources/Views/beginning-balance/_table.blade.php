<table class="table table-hover align-middle border">
    <thead class="table-light">
        <tr>
            <th width="150">Kode Akun</th>
            <th>Nama Akun</th>
            <th>Kategori</th>
            <th width="300" class="text-end">Saldo Awal (IDR)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($coas as $index => $coa)
        <tr>
            <td class="font-monospace">{{ $coa->code }}</td>
            <td>{{ $coa->name }}</td>
            <td><span class="badge bg-light text-dark border">{{ strtoupper($coa->category->value) }}</span></td>
            <td>
                <input type="hidden" name="balances[{{ $index }}][coa_id]" value="{{ $coa->id }}">
                <div class="input-group">
                    <span class="input-group-text bg-light">Rp</span>
                    <input type="number" step="0.01"
                           name="balances[{{ $index }}][amount]"
                           class="form-control text-end"
                           value="{{ old('balances.'.$index.'.amount', $existingBalances[$coa->id] ?? 0) }}"
                           {{ isset($periods->find($selectedPeriodId)->is_closed) && $periods->find($selectedPeriodId)->is_closed ? 'readonly' : '' }}>
                </div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
