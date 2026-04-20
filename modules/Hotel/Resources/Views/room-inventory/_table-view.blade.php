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
                            {{-- Gunakan badgeClass dari Enum untuk warna avatar agar seragam dengan index --}}
                            <div class="avatar-xs {{ $inv->type->badgeClass() }} rounded p-2 me-3 text-center">
                                <i class="mdi {{ $inv->type === \Modules\Hotel\Enums\InventoryTypeEnum::ASSET ? 'mdi-package-variant-closed' : 'mdi-shimmer' }} fs-5"></i>
                            </div>
                            <div>
                                <span class="fw-bold d-block text-dark">{{ $inv->name }}</span>
                                <span class="text-muted small">Tipe: {{ $inv->type->label() }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        {{-- Menampilkan jumlah yang dialokasikan ke kamar dari tabel pivot --}}
                        <div class="d-inline-block px-3 py-1 rounded-pill bg-soft-primary text-primary fw-bold">
                            {{ number_format($inv->pivot->quantity) }} {{ $inv->unit }}
                        </div>
                    </td>
                    <td class="pe-4 text-muted small">
                        {{ $inv->pivot->note ?? '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center py-5">
                        <div class="text-muted opacity-50">
                            <i class="mdi mdi-archive-off-outline" style="font-size: 3rem;"></i>
                            <p class="mt-2">Belum ada inventaris yang diset untuk kamar ini.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
