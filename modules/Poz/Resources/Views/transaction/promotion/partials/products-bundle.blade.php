@push('styles')
<style>
    .bundle-item-row {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 10px;
        margin-bottom: 8px;
        border: 1px solid #dee2e6;
    }
    .select2-container { width: 100% !important; }
    .animated { animation-duration: 0.3s; animation-fill-mode: both; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    .fadeIn { animation-name: fadeIn; }
</style>
@endpush

<div id="config-bundle" class="card border-0 shadow-sm mb-4" style="display:none;">
    <div class="card-header bg-success text-white py-3">
        <h6 class="mb-0 fw-bold">Konfigurasi Bundle / Kategori</h6>
    </div>
    <div class="card-body">
        <div class="mb-4">
            <label class="form-label fw-bold">Pilih Mode Bundle</label>
            @php
                $currentMode = 'product';
                if(isset($promotion->config['bundle_categories']) && count($promotion->config['bundle_categories']) > 0) {
                    $currentMode = 'category';
                }
            @endphp
            <select id="bundle_mode" class="form-select form-select-lg border-success">
                <option value="product" {{ $currentMode == 'product' ? 'selected' : '' }}>Berdasarkan Daftar Produk</option>
                <option value="category" {{ $currentMode == 'category' ? 'selected' : '' }}>Berdasarkan Kategori</option>
            </select>
        </div>

        <div id="section-bundle-product" class="bundle-section {{ $currentMode != 'product' ? 'd-none' : '' }}">
            <label class="form-label fw-semibold text-success">Pilih Produk & Atur Qty</label>
            <select id="select-bundle-products" class="form-select select2-promo" multiple>
                @foreach($products as $p)
                    @php
                        $isSelected = isset($promotion->config['bundle_products'][$p->id]);
                    @endphp
                    <option value="{{ $p->id }}" data-name="{{ $p->name }}" {{ $isSelected ? 'selected' : '' }}>
                        {{ $p->name }}
                    </option>
                @endforeach
            </select>

            <div id="bundle-product-list" class="mt-3">
                @if(isset($promotion->config['bundle_products']))
                    @foreach($promotion->config['bundle_products'] as $id => $bp)
                        <div class="bundle-item-row d-flex align-items-center gap-2" data-id="{{ $id }}">
                            <div class="flex-grow-1"><strong>{{ $bp['name'] ?? 'Produk' }}</strong></div>
                            <div style="width: 130px;">
                                <div class="input-group input-group-sm">
                                    <input type="number" name="config[bundle_products][{{ $id }}][qty]" class="form-control" value="{{ $bp['qty'] }}" min="1">
                                    <span class="input-group-text">Pcs</span>
                                </div>
                                <input type="hidden" name="config[bundle_products][{{ $id }}][id]" value="{{ $id }}">
                                <input type="hidden" name="config[bundle_products][{{ $id }}][name]" value="{{ $bp['name'] ?? '' }}">
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <div id="section-bundle-category" class="bundle-section {{ $currentMode != 'category' ? 'd-none' : '' }}">
            <label class="form-label fw-semibold text-success">Pilih Kategori & Atur Qty</label>
            <select id="select-bundle-categories" class="form-select select2-promo" multiple>
                @foreach($categories as $c)
                    @php
                        $isSelected = isset($promotion->config['bundle_categories'][$c->id]);
                    @endphp
                    <option value="{{ $c->id }}" data-name="{{ $c->name }}" {{ $isSelected ? 'selected' : '' }}>
                        {{ $c->name }}
                    </option>
                @endforeach
            </select>

            <div id="bundle-category-list" class="mt-3">
                @if(isset($promotion->config['bundle_categories']))
                    @foreach($promotion->config['bundle_categories'] as $id => $bc)
                        <div class="bundle-item-row d-flex align-items-center gap-2" data-id="{{ $id }}">
                            <div class="flex-grow-1"><strong>{{ $bc['name'] ?? 'Kategori' }}</strong></div>
                            <div style="width: 130px;">
                                <div class="input-group input-group-sm">
                                    <input type="number" name="config[bundle_categories][{{ $id }}][qty]" class="form-control" value="{{ $bc['qty'] }}" min="1">
                                    <span class="input-group-text">Min</span>
                                </div>
                                <input type="hidden" name="config[bundle_categories][{{ $id }}][id]" value="{{ $id }}">
                                <input type="hidden" name="config[bundle_categories][{{ $id }}][name]" value="{{ $bc['name'] ?? '' }}">
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <div class="row border-top pt-3 mt-4">
            <div class="col-md-12">
                <label class="form-label fw-bold">Harga Promo (Per Item dalam Bundle)</label>
                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-success text-white">Rp</span>
                    <input type="number" name="config[special_price]" class="form-control" value="{{ $promotion->config['special_price'] ?? '' }}" placeholder="0">
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    const bundleMode = $('#bundle_mode');
    const productSelect = $('#select-bundle-products');
    const categorySelect = $('#select-bundle-categories');

    function createRow(id, name, type) {
        const inputName = type === 'product' ? `config[bundle_products][${id}]` : `config[bundle_categories][${id}]`;
        const unit = type === 'product' ? 'Pcs' : 'Min';

        return `
            <div class="bundle-item-row d-flex align-items-center gap-2 animated fadeIn" data-id="${id}">
                <div class="flex-grow-1"><strong>${name}</strong></div>
                <div style="width: 130px;">
                    <div class="input-group input-group-sm">
                        <input type="number" name="${inputName}[qty]" class="form-control" value="1" min="1">
                        <span class="input-group-text">${unit}</span>
                    </div>
                    <input type="hidden" name="${inputName}[id]" value="${id}">
                    <input type="hidden" name="${inputName}[name]" value="${name}">
                </div>
            </div>
        `;
    }

    function toggleBundleSections() {
        const mode = bundleMode.val();
        $('.bundle-section').addClass('d-none');
        if (mode === 'product') {
            $('#section-bundle-product').removeClass('d-none');
        } else {
            $('#section-bundle-category').removeClass('d-none');
        }

        if (typeof initSelect2Promo === "function") {
            initSelect2Promo();
        }
    }

    bundleMode.on('change', toggleBundleSections);

    productSelect.on('select2:select', function(e) {
        const data = e.params.data;
        if($(`#bundle-product-list [data-id="${data.id}"]`).length === 0) {
            $('#bundle-product-list').append(createRow(data.id, $(data.element).data('name'), 'product'));
        }
    }).on('select2:unselect', function(e) {
        $(`#bundle-product-list .bundle-item-row[data-id="${e.params.data.id}"]`).remove();
    });

    categorySelect.on('select2:select', function(e) {
        const data = e.params.data;
        if($(`#bundle-category-list [data-id="${data.id}"]`).length === 0) {
            $('#bundle-category-list').append(createRow(data.id, $(data.element).data('name'), 'category'));
        }
    }).on('select2:unselect', function(e) {
        $(`#bundle-category-list .bundle-item-row[data-id="${e.params.data.id}"]`).remove();
    });

    toggleBundleSections();
});
</script>
@endpush
