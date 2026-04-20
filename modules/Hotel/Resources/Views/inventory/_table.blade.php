<div class="table-responsive">
    <table class="table align-middle table-hover mb-0">
        <thead class="bg-light">
            <tr>
                <th class="ps-4" style="min-width: 200px;">Nama Barang & Tipe</th>
                <th class="text-center">Stok Saat Ini</th>
                <th class="text-center">Batas Minimum</th>
                <th>Deskripsi</th>
                <th class="text-end pe-4">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($inventories as $inventory)
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center">
                            <div class="avatar-xs {{ $inventory->type->badgeClass() }} rounded p-2 me-3 text-center">
                                <i class="mdi {{ $inventory->type === \Modules\Hotel\Enums\InventoryTypeEnum::ASSET ? 'mdi-package-variant-closed' : 'mdi-shimmer' }} fs-4"></i>
                            </div>
                            <div>
                                <span class="fw-bold d-block text-dark">{{ $inventory->name }}</span>
                                {{-- Gunakan method label() dari Enum --}}
                                <span class="badge {{ $inventory->type->badgeClass() }} font-size-10">
                                    {{ strtoupper($inventory->type->label()) }}
                                </span>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="fw-bold fs-5 {{ $inventory->total_stock <= $inventory->min_stock ? 'text-danger' : 'text-dark' }}">
                            {{ $inventory->total_stock }}
                        </span>
                        <span class="text-muted small">{{ $inventory->unit }}</span>

                        @if($inventory->total_stock <= $inventory->min_stock)
                            <div class="d-block mt-1">
                                <span class="badge bg-danger rounded-pill px-2" style="font-size: 9px;">
                                    <i class="mdi mdi-alert-circle-outline"></i> LOW STOCK
                                </span>
                            </div>
                        @endif
                    </td>
                    <td class="text-center text-muted">
                        {{ $inventory->min_stock }} {{ $inventory->unit }}
                    </td>
                    <td>
                        <small class="text-muted d-block text-truncate" style="max-width: 150px;" title="{{ $inventory->description }}">
                            {{ $inventory->description ?: '-' }}
                        </small>
                    </td>
                    <td class="text-end pe-4">
                        <div class="btn-group">
                            <a href="{{ route('hotel::inventory.edit', $inventory->id) }}"
                               class="btn btn-sm btn-light border shadow-sm"
                               title="Edit Barang">
                                <i class="mdi mdi-pencil-outline"></i>
                            </a>
                            <button type="button"
                                    class="btn btn-sm btn-light border text-danger shadow-sm"
                                    onclick="deleteInventory({{ $inventory->id }})"
                                    title="Hapus Barang">
                                <i class="mdi mdi-trash-can-outline"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <div class="mb-3">
                            <i class="mdi mdi-archive-remove-outline text-muted" style="font-size: 3rem;"></i>
                        </div>
                        <h5 class="text-muted">Belum ada data inventaris</h5>
                        <p class="text-muted small">Klik tombol "Tambah Barang" untuk memulai.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
