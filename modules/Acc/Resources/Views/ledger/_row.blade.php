<tr>
    <td>
        <select name="entries[{{ $index }}][coa_id]" class="form-select select2" required>
            <option value="">-- Pilih Akun --</option>
            @foreach($coas as $coa)
                <option value="{{ $coa->id }}" {{ isset($entry) && $entry->coa_id == $coa->id ? 'selected' : '' }}>
                    {{ $coa->code }} - {{ $coa->name }} ({{ strtoupper($coa->normal_balance->value) }})
                </option>
            @endforeach
        </select>
    </td>
    <td>
        <input type="number" step="0.01" name="entries[{{ $index }}][debit]"
               class="form-control text-end input-debit"
               value="{{ $entry->debit ?? 0 }}" required>
    </td>
    <td>
        <input type="number" step="0.01" name="entries[{{ $index }}][credit]"
               class="form-control text-end input-credit"
               value="{{ $entry->credit ?? 0 }}" required>
    </td>
    <td class="text-center">
        <button type="button" class="btn btn-sm btn-link text-danger" onclick="removeRow(this)">
            <i class="mdi mdi-delete"></i>
        </button>
    </td>
</tr>
