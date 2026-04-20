<div class="modal fade" id="modalAdjustment" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('hotel::inventory-adjustment.store') }}" method="POST">
            @csrf
            <input type="hidden" name="inventory_id" value="{{ $inventory->id }}">

            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Adjustment Stok</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Tipe</label>
                            <select name="status" class="form-select shadow-none" required>
                                <option value="plus">Tambah (+)</option>
                                <option value="minus">Kurang (-)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Jumlah ({{ $inventory->unit }})</label>
                            <input type="number" name="quantity" class="form-control shadow-none" min="1" required placeholder="0">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Keterangan</label>
                            <textarea name="note" class="form-control shadow-none" rows="3" placeholder="Isian catatan stok..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Simpan Data</button>
                </div>
            </div>
        </form>
    </div>
</div>
