@extends('poz::layout.index')

@section('title', 'Konfigurasi Varian - ' . $product->name)

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-xl-11">
            <form action="{{ route('poz::transaction.product-variant.store') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}" />
                <input type="hidden" name="has_variant" value="{{ $tierCount > 0 ? 'yes' : 'no' }}">

                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                        <div>
                            <h5 class="mb-0 fw-bold text-dark">Manajemen Varian & Harga</h5>
                            <small class="text-muted">Master Produk: {{ $product->name }}</small>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('poz::transaction.product.index') }}" class="btn btn-light btn-sm px-3">Batal</a>
                            <button type="submit" class="btn btn-primary btn-sm px-3 shadow-sm">
                                <i class="fa fa-save me-1"></i> Simpan Konfigurasi
                            </button>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        @if($tierCount > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle" id="variantTable">
                                    <thead class="bg-light">
                                        <tr class="text-uppercase small fw-bold text-secondary">
                                            <th class="py-3 px-3">{{ $tier1->name ?? 'Tier 1' }}</th>
                                            @if($tierCount == 2) <th class="py-3">{{ $tier2->name ?? 'Tier 2' }}</th> @endif
                                            <th class="py-3">Kode Varian</th>
                                            <th class="py-3">Harga Beli</th>
                                            <th class="py-3">Harga Jual</th>
                                            <th class="py-3" style="width: 100px;">Alert</th>
                                            <th class="py-3 text-center" style="width: 50px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $displayVariants = collect($savedVariants)->where('status', '!=', 'deleted')->where('variant_type', '!=', 'no_variant')->all();
                                        @endphp

                                        @foreach(count($displayVariants) > 0 ? $displayVariants : [null] as $item)
                                        <tr class="variant-row">
                                            <td class="px-3">
                                                <select name="tier_1_ids[]" class="form-select form-select-sm border-0 bg-light" required>
                                                    <option value="">Pilih...</option>
                                                    @foreach($options1 as $opt)
                                                        <option value="{{ $opt->id }}" {{ isset($item['tier_1_id']) && $item['tier_1_id'] == $opt->id ? 'selected' : '' }}>{{ $opt->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            @if($tierCount == 2)
                                            <td>
                                                <select name="tier_2_ids[]" class="form-select form-select-sm border-0 bg-light" required>
                                                    <option value="">Pilih...</option>
                                                    @foreach($options2 as $opt)
                                                        <option value="{{ $opt->id }}" {{ isset($item['tier_2_id']) && $item['tier_2_id'] == $opt->id ? 'selected' : '' }}>{{ $opt->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            @endif
                                            <td>
                                                <input type="text" name="codes[]" class="form-control form-control-sm border-0 bg-light fw-bold" value="{{ $item['code'] ?? $product->code . '-' . rand(100,999) }}" required>
                                            </td>
                                            <td>
                                                <input type="number" name="wholesales[]" class="form-control form-control-sm border-0 bg-light" value="{{ $item['wholesale'] ?? (int)$product->wholesale }}">
                                            </td>
                                            <td>
                                                <input type="number" name="prices[]" class="form-control form-control-sm border-0 bg-light fw-bold text-primary" value="{{ $item['price'] ?? (int)$product->price }}">
                                            </td>
                                            <td>
                                                <input type="number" name="alert_qtys[]" class="form-control form-control-sm border-0 bg-light text-center" value="{{ $item['alert_qty'] ?? 5 }}">
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-link text-danger p-0 remove-row"><i class="fa fa-trash-alt"></i></button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm w-100 border-dashed mt-2" id="btnAddRow">+ Tambah Kombinasi Varian</button>
                        @else
                            <div class="row g-3">
                                @php $single = collect($savedVariants)->where('variant_type', 'no_variant')->first(); @endphp
                                <div class="col-md-3">
                                    <label class="small fw-bold text-muted">Kode Produk</label>
                                    <input type="text" name="product_code" class="form-control bg-light border-0 fw-bold" value="{{ $single['code'] ?? $product->code }}" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="small fw-bold text-muted">Harga Beli</label>
                                    <input type="number" name="single_wholesale" class="form-control bg-light border-0" value="{{ $single['wholesale'] ?? (int)$product->wholesale }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="small fw-bold text-muted">Harga Jual</label>
                                    <input type="number" name="single_price" class="form-control bg-light border-0 fw-bold text-primary" value="{{ $single['price'] ?? (int)$product->price }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="small fw-bold text-muted">Alert Qty</label>
                                    <input type="number" name="single_alert_qty" class="form-control bg-light border-0 text-center" value="{{ $single['alert_qty'] ?? 5 }}">
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .border-dashed { border-style: dashed !important; border-width: 2px; }
    .form-select-sm, .form-control-sm { border-radius: 4px; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tableBody = document.querySelector('#variantTable tbody');
        const btnAdd = document.querySelector('#btnAddRow');

        if(btnAdd) {
            btnAdd.addEventListener('click', function() {
                const rows = tableBody.querySelectorAll('tr');
                const clone = rows[0].cloneNode(true);
                clone.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
                clone.querySelectorAll('input').forEach(i => {
                    if(i.name === 'codes[]') i.value = "{{ $product->code }}-" + Math.floor(Math.random() * 900 + 100);
                    if(i.name === 'alert_qtys[]') i.value = 5;
                });
                tableBody.appendChild(clone);
            });

            tableBody.addEventListener('click', function(e) {
                const btn = e.target.closest('.remove-row');
                if (btn && tableBody.querySelectorAll('tr').length > 1) {
                    btn.closest('tr').remove();
                }
            });
        }
    });
</script>
@endsection
