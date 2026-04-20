<div class="table-responsive">
    <table class="table align-middle table-hover mb-0">
        <thead class="bg-light text-uppercase">
            <tr>
                <th class="ps-4" style="width: 50px;">Pilih</th>
                <th style="font-size: 11px; letter-spacing: 1px;">Barang Master</th>
                <th width="200" class="text-center" style="font-size: 11px; letter-spacing: 1px;">Qty di Kamar</th>
                <th style="font-size: 11px; letter-spacing: 1px;">Catatan Unit</th>
            </tr>
        </thead>
        <tbody>
            @forelse($allInventories as $item)
                @php
                    // Ambil data yang nempel di kamar ini sekarang
                    $pivotData = $room->inventories->where('id', $item->id)->first();
                    $isChecked = $pivotData ? 'checked' : '';
                    $currentInRoom = $pivotData ? $pivotData->pivot->quantity : 0;

                    // Karena kita pakai sistem 'Decrement Fisik',
                    // maka total_stock di DB adalah sisa gudang.
                    // Max Allowed = Sisa Gudang + Yang sudah ada di kamar ini
                    $maxAllowed = $item->total_stock + $currentInRoom;
                @endphp
                <tr>
                    <td class="ps-4">
                        <div class="form-check form-switch fs-4">
                            <input class="form-check-input inventory-checkbox" type="checkbox"
                                name="inventory_ids[]" value="{{ $item->id }}" {{ $isChecked }}
                                {{ $maxAllowed <= 0 && !$isChecked ? 'disabled' : '' }}>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-xs bg-soft-primary rounded p-2 me-3 text-center">
                                <i class="mdi {{ $item->type->value === 1 ? 'mdi-package-variant-closed' : 'mdi-shimmer' }} text-primary fs-5"></i>
                            </div>
                            <div>
                                <span class="fw-bold d-block text-dark">{{ $item->name }}</span>
                                <div class="d-flex align-items-center gap-2" style="font-size: 11px;">
                                    <span class="text-muted small">Tersedia di Gudang: </span>
                                    <span class="{{ $item->total_stock > 0 ? 'text-success' : 'text-danger' }} fw-bold">
                                        {{ $item->total_stock }} {{ $item->unit }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="input-group input-group-sm px-3">
                            <input type="number" name="quantities[{{ $item->id }}]"
                                class="form-control text-center fw-bold inventory-qty"
                                value="{{ $currentInRoom ?: ($maxAllowed > 0 ? 1 : 0) }}"
                                min="1"
                                max="{{ $maxAllowed }}"
                                {{ !$isChecked ? 'disabled' : '' }}>
                            <span class="input-group-text bg-light">{{ $item->unit }}</span>
                        </div>
                        @if($maxAllowed <= 0 && !$isChecked)
                            <small class="text-danger d-block text-center mt-1" style="font-size: 10px;">Stok Gudang Habis!</small>
                        @endif
                    </td>
                    <td class="pe-4">
                        <input type="text" name="notes[{{ $item->id }}]"
                            class="form-control form-control-sm inventory-note"
                            value="{{ $pivotData ? $pivotData->pivot->note : '' }}"
                            placeholder="Catatan..." {{ !$isChecked ? 'disabled' : '' }}>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center py-5 text-muted">Master Inventaris kosong.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
