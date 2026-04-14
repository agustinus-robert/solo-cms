<div class="pos-main-layout">
    <div class="pos-left-section">
    <div class="card border-0 shadow-sm rounded-4 pos-card-wrapper-manual">
        <div class="card-body p-3">
            @include('poz::transaction.sale.partials.search')

            <div class="scroll-y-products-manual mt-3">
                <div class="row g-2">
                    @foreach($products as $p)
                        @php
                            $allVariants = [];

                            if ($p->variant) {
                                foreach ($p->variant as $vRow) {
                                    $vData = is_string($vRow->product_variant) ? json_decode($vRow->product_variant) : $vRow->product_variant;

                                    if (is_array($vData)) {
                                        foreach ($vData as $v) {
                                            $vObj = (object) $v;
                                            if (($vObj->status ?? '') !== 'deleted' && ($vObj->deleted_at ?? null) === null) {
                                                $mutations = collect($stocks)->where('variant_code', $vObj->code);

                                                $realStock = $mutations->reduce(function($carry, $item) {
                                                    return $item->status === 'plus' ? $carry + $item->qty : $carry - $item->qty;
                                                }, 0);

                                                $vObj->real_stock = $realStock < 0 ? 0 : $realStock;
                                                $allVariants[] = $vObj;
                                            }
                                        }
                                    }
                                }
                            }

                            if (empty($allVariants)) {
                                $mutations = collect($stocks)->where('variant_code', $p->code);

                                $pStock = $mutations->reduce(function($carry, $item) {
                                    return $item->status === 'plus' ? $carry + $item->qty : $carry - $item->qty;
                                }, 0);

                                $allVariants[] = (object)[
                                    'code' => $p->code,
                                    'name' => 'Default',
                                    'price' => $p->price,
                                    'real_stock' => $pStock < 0 ? 0 : $pStock,
                                    'variant_type' => 'no_variant'
                                ];
                            }

                            $totalStock = collect($allVariants)->sum('real_stock');
                            $isOut = $totalStock <= 0;
                            $isSingle = count($allVariants) === 1;
                        @endphp

                        <div class="col-6 col-md-4 col-xl-3 mb-2">
                            <div class="card h-100 product-card-item rounded-3 {{ $isOut ? 'is-out' : '' }} {{ ($isSingle && !$isOut) ? 'cursor-pointer' : '' }}"
                                 @if($isSingle && !$isOut)
                                    onclick="addItemToCart({{ json_encode($p) }}, {{ json_encode($allVariants[0]) }})"
                                 @endif>

                                @if($isOut)
                                    <div class="position-absolute top-50 start-50 translate-middle bg-danger text-white px-2 py-1 rounded small fw-bold" style="z-index: 10;">
                                        HABIS
                                    </div>
                                @endif

                                <div class="p-2 text-center">
                                    <img src="{{ $p->image_name ? asset('uploads/'.$p->location.'/'.$p->image_name) : asset('assets/img/no-image.png') }}"
                                         class="rounded-2" style="height: 100px; width: 100%; object-fit: cover;">
                                </div>

                                <div class="card-body p-2 text-center text-dark">
                                    <h6 class="small fw-bold mb-1 text-truncate" title="{{ $p->name }}">{{ $p->name }}</h6>
                                    <div class="text-primary small fw-bold mb-2">Rp {{ number_format($p->price, 0, ',', '.') }}</div>

                                    <div class="d-flex flex-wrap justify-content-center gap-1">
                                        @foreach($allVariants as $v)
                                            <button type="button"
                                                    data-variant-code="{{ $v->code }}"
                                                    class="btn btn-xs btn-add-to-cart {{ $v->real_stock > 0 ? 'btn-outline-primary' : 'btn-light disabled text-muted' }} py-0 px-1"
                                                    style="font-size: 9px;"
                                                    onclick="event.stopPropagation(); addItemToCart({{ json_encode($p) }}, {{ json_encode($v) }})">
                                                {{ ($v->name == 'Default' || $v->variant_type == 'no_variant') ? 'Stok' : $v->name }}: {{ $v->real_stock }}
                                            </button>
                                        @endforeach
                                    </div>
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
        <div class="card border-0 shadow-sm rounded-4 mb-3 card-keranjang-manual">
            <div class="card-header bg-white border-0 pt-3 pb-0">
                <h6 class="fw-bold mb-0 small text-uppercase text-secondary border-bottom pb-2">
                    <i class="fa fa-shopping-cart me-2"></i>Daftar Belanja
                </h6>
            </div>

            <div class="card-body p-0 cart-body-scroll-manual">
                <table class="table table-hover align-middle mb-0 table-fixed-cart">
                    <thead class="bg-light sticky-top">
                        <tr style="font-size: 11px;">
                            <th class="border-0 px-3 col-produk">PRODUK</th>
                            <th class="border-0 text-center col-qty">QTY</th>
                            <th class="border-0 text-end px-3 col-total">TOTAL</th>
                        </tr>
                    </thead>
                    <tbody id="selectedItemsBody">
                        <tr>
                            <td colspan="3" class="text-center py-5 text-muted small">Belum ada barang dipilih.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card border-0 shadow-sm border-top border-primary border-4 rounded-4">
            <div class="card-body p-3">
                @include('poz::transaction.sale.partials.summary')
            </div>
        </div>
    </div>
</div>

@include('poz::transaction.sale.partials.pos-scripts')
