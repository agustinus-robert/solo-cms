<div>
    <div class="row mb-4 align-items-center">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <div>
                <h4 class="fw-bold mb-1">Produk</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0" style="font-size: 0.85rem;">
                        <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted">Inventori</a></li>
                        <li class="breadcrumb-item active text-primary">{{ $action }} Produk</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-bottom-0 pt-4 px-4">
            <h5 class="card-title mb-0 fw-bold">{{ $action == 'direction' ? 'Direct Production / Entry' : $action . ' Produk' }}</h5>
        </div>

        <form wire:submit="save" enctype="multipart/form-data">
            <div class="card-body p-4">
                <div class="row g-4">

                    @if ($action == 'direction')
                        {{-- VIEW DIRECTION --}}
                        <div class="col-md-7 border-end">
                            <h6 class="fw-bold text-uppercase text-muted mb-3" style="font-size: 0.75rem;">Informasi Dasar</h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Code</label>
                                    <input disabled wire:model="form.code" type="text" class="form-control bg-light">
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label small fw-bold">Nama Produk</label>
                                    <input type="text" class="form-control" wire:model="form.name">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Kategori</label>
                                    <select class="form-select" wire:model="form.category_id" wire:change="sub_category_changed($event.target.value)">
                                        <option value="">Pilih Kategori</option>
                                        @foreach ($category as $value) <option value="{{ $value->id }}">{{ $value->name }}</option> @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Sub Kategori</label>
                                    <select class="form-select" wire:model="form.sub_category_id" {{ $categoryHasSub != 1 ? 'disabled' : '' }}>
                                        <option value="">Pilih Sub</option>
                                        @foreach ($subCategory as $value) <option value="{{ $value->id }}">{{ $value->name }}</option> @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Harga Jual</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">Rp</span>
                                        <input type="number" wire:model="form.price" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Unit</label>
                                    <select class="form-select" wire:model="form.unit_id">
                                        <option value="">Pilih</option>
                                        <option value="1">PCS</option>
                                    </select>
                                </div>
                                {{-- TinyMCE Direction --}}
                                <div class="col-12" wire:ignore>
                                    <label class="form-label small fw-bold">Deskripsi</label>
                                    <textarea id="description" class="form-control" wire:model="form.description"></textarea>
                                </div>

                                <input type="hidden" wire:model="form.type" value="standard">
                                <input type="hidden" wire:model="form.barcode" value="1">
                            </div>
                        </div>

                        <div class="col-md-5">
                            <h6 class="fw-bold text-uppercase text-muted mb-3" style="font-size: 0.75rem;">Input Stok & Konfigurasi</h6>
                            <div class="bg-light p-3 rounded-3 mb-3">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Supplier</label>
                                    <select class="form-select border-0 shadow-none" wire:model="sch.supplier">
                                        <option value="">Pilih Supplier</option>
                                        @foreach($supplier as $sup) <option value="{{ $sup->id }}">{{$sup->name}}</option> @endforeach
                                    </select>
                                </div>
                                <div class="row g-2 mb-0">
                                    <div class="col-7">
                                        <label class="form-label small fw-bold">Shift</label>
                                        <select class="form-select border-0 shadow-none" wire:model="sch.shifts">
                                            <option value="">Pilih</option>
                                            <option value="morning">Morning</option>
                                            <option value="afternoon">Afternoon</option>
                                            <option value="evening">Evening</option>
                                        </select>
                                    </div>
                                    <div class="col-5">
                                        <label class="form-label small fw-bold">Qty Input</label>
                                        <input class="form-control border-0 shadow-none" type="number" wire:model="sch.qty" placeholder="0">
                                    </div>
                                </div>
                            </div>

                            {{-- Visibilitas Card Direction --}}
                            <div class="p-3 border rounded-3 bg-white">
                                <h6 class="fw-bold text-uppercase text-muted mb-3" style="font-size: 0.75rem;">Visibilitas</h6>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="posDir" wire:model="form.is_pos" checked>
                                    <label class="form-check-label small fw-bold" for="posDir">Tampilkan di POS</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="ecommerceDir" wire:model="form.is_ecommerce">
                                    <label class="form-check-label small fw-bold" for="ecommerceDir">Tampilkan di E-commerce</label>
                                </div>
                            </div>
                        </div>

                    @else
                        @include('poz::livewire.transaction.product-partials.left-form')
                        @include('poz::livewire.transaction.product-partials.right-form')
                    @endif
                </div>
            </div>

            <div class="card-footer bg-white p-4 border-top-0 d-flex align-items-center justify-content-between">
                <button type="button" wire:click="testNotification" class="btn btn-outline-info px-4 me-2">
                    🚀 Test Realtime
                </button>

                <div>
                    <div wire:loading class="spinner-border spinner-border-sm text-primary me-2"></div>
                    <span wire:loading class="small text-muted">Memproses data...</span>
                </div>
                <div>
                    <button type="button" class="btn btn-link text-decoration-none text-muted me-3">Batal</button>
                    <button type="submit" wire:loading.attr="disabled" class="btn btn-primary px-5 shadow-sm">
                        Simpan Produk
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('vendor/tinymce/tinymce.min.js') }}"></script>
    <script>
        tinymce.init({
            selector: 'textarea#description',
            height: "300",
            paste_data_images: true,
            relative_urls: false,
            plugins: 'autosave autoresize preview paste searchreplace code fullscreen image link media table charmap hr pagebreak advlist lists wordcount',
            menubar: false,
            toolbar: "formatselect bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist | preview code",
            setup: function (editor) {
                editor.on('blur', function (e) {
                    @this.set('form.description', editor.getContent());
                });
            }
        });
    </script>
@endpush
