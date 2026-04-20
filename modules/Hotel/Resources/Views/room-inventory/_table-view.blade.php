<div class="table-responsive">
    <table class="table align-middle table-hover mb-0">
        <thead class="bg-light text-uppercase">
            <tr>
                <th class="ps-4" style="font-size: 11px; letter-spacing: 1px;">Nama Barang</th>
                <th width="200" class="text-center" style="font-size: 11px; letter-spacing: 1px;">Jumlah Saat Ini</th>
                <th style="font-size: 11px; letter-spacing: 1px;">Catatan Unit</th>
            </tr>
        </thead>
        <tbody>
            @forelse($room->inventories as $inv)
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center">
                            <div class="avatar-xs bg-soft-info rounded p-2 me-3 text-center">
                                <i class="mdi {{ $inv->type->value === 1 ? 'mdi-package-variant-closed' : 'mdi-shimmer' }} text-info fs-5"></i>
                            </div>
                            <div>
                                <span class="fw-bold d-block text-dark">{{ $inv->name }}</span>
                                <span class="text-muted small">Tipe: {{ $inv->type->label() }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="d-inline-block px-3 py-1 rounded-pill bg-soft-primary text-primary fw-bold">
                            {{ $inv->pivot->quantity }} {{ $inv->unit }}
                        </div>
                    </td>
                    <td class="pe-4 text-muted">
                        {{ $inv->pivot->note ?? '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center py-5">
                        <div class="text-muted opacity-50">
                            <i class="mdi mdi-archive-off-outline mdi-48px"></i>
                            <p class="mt-2">Belum ada inventaris yang diset untuk kamar ini.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
