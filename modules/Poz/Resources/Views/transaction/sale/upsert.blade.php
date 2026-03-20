@php
    $isPos = request()->query('pos') === 'true';
@endphp

@push('styles')
<style>
    /* --- CSS GLOBAL --- */
    .transition-all { transition: all 0.2s ease; }
    .product-card-item { transition: all 0.2s; border: 1px solid #eee; cursor: pointer; background: #fff; }
    .product-card-item:hover { transform: translateY(-3px); border-color: #0d6efd !important; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }

    /* --- LAYOUT POS MODE --- */
    .pos-mode {
        background-color: #f4f7f6 !important;
        overflow-y: auto !important;
    }

    .pos-mode .pos-container {
        padding: 20px;
        min-height: 100vh;
    }

    .pos-mode .pos-main-layout {
        display: flex;
        align-items: flex-start;
        gap: 20px;
        margin-top: 15px;
    }

    /* Kiri: Grid Produk Sticky */
    .pos-mode .pos-left-section {
        flex: 7;
        position: sticky;
        top: 20px;
        display: flex;
        flex-direction: column;
        max-height: calc(100vh - 40px);
        min-width: 0;
    }

    /* Kanan: Sidebar Panjang Ke Bawah */
    .pos-mode .pos-right-sidebar {
        flex: 5;
        display: flex;
        flex-direction: column;
        gap: 15px;
        min-width: 400px;
    }

    .pos-mode .scroll-y-products {
        overflow-y: auto;
        flex: 1;
        padding-right: 5px;
    }

    /* Styling Empty State */
    .cart-empty-state {
        padding: 40px 20px;
        text-align: center;
        color: #adb5bd;
    }
</style>
@endpush

@if($isPos)
    <script>document.body.classList.add('pos-mode');</script>
@endif

<div class="{{ $isPos ? 'pos-container' : 'container-fluid py-4' }}">

    @include('poz::transaction.sale.partials.header')

    <form id="saleForm" action="{{ route('poz::transaction.sale.store') }}" method="POST">
        @csrf
        @if($sale) <input type="hidden" name="id" value="{{ $sale->id }}"> @endif
        <input type="hidden" name="outlet_id" value="{{ $outletId }}">

        @if($isPos)
            <div class="pos-main-layout">

                <div class="pos-left-section">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-3 d-flex flex-column">
                            @include('poz::transaction.sale.partials.search')

                            <div class="scroll-y-products mt-3">
                                <div class="row g-2">
                                    @foreach($products as $p)
                                    <div class="col-6 col-md-4 col-xl-3">
                                        <div class="card h-100 product-card-item rounded-3"
                                             onclick="addProductToCart({{ json_encode($p) }})">
                                            <div class="p-2 text-center">
                                                <img src="{{ asset('uploads/'.$p->location.'/'.$p->image_name) ?? asset('assets/img/no-image.png') }}"
                                                     class="rounded-2" style="height: 100px; width: 100%; object-fit: cover;">
                                            </div>
                                            <div class="card-body p-2 text-center text-dark">
                                                <h6 class="small fw-bold mb-1 text-truncate">{{ $p->name }}</h6>
                                                <div class="text-primary small fw-bold">Rp {{ number_format($p->price, 0, ',', '.') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pos-right-sidebar">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-0 pt-3 pb-0">
                            <h6 class="fw-bold mb-0 small text-uppercase text-secondary border-bottom pb-2">
                                <i class="fa fa-shopping-cart me-2"></i>Daftar Belanja
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0" id="cartTable">
                                    <thead class="bg-light">
                                        <tr style="font-size: 11px;">
                                            <th class="border-0 px-3">PRODUK</th>
                                            <th class="border-0 text-center" width="100">QTY</th>
                                            <th class="border-0 text-end px-3" width="120">SUBTOTAL</th>
                                        </tr>
                                    </thead>
                                    <tbody id="cartTableBody">
                                        <tr>
                                            <td colspan="3" class="cart-empty-state">
                                                <i class="fa fa-shopping-basket fa-2x mb-2 d-block opacity-25"></i>
                                                <span class="small">Belum ada produk. Klik produk di kiri untuk menambahkan.</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-3">
                            @include('poz::transaction.sale.partials.summary')
                        </div>
                    </div>
                </div>

            </div>
        @else
            <div class="row g-4 mt-2">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm p-4 text-dark">
                        @include('poz::transaction.sale.partials.search')
                        <div class="mt-3">
                            @include('poz::transaction.sale.partials.table')
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    @include('poz::transaction.sale.partials.summary')
                </div>
            </div>
        @endif
    </form>
</div>

@push('scripts')
    @include('poz::transaction.sale.partials.scripts')

    <script>
        /**
         * Logic Tambah Produk
         */
        function addProductToCart(productData) {
            // Kita panggil fungsi selectProduct yang ada di scripts.blade.php
            if (typeof selectProduct === 'function') {
                selectProduct(productData);
            } else {
                // Fallback sederhana jika scripts.blade.php belum siap
                console.log("Menambah produk:", productData.name);
            }
        }
    </script>
@endpush
