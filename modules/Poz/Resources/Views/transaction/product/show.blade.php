@extends('poz::layout.index')

@section('title', env('APP_NAME') . ' - Pengelolaan Produk')

@section('navtitle', 'Manajemen Pengelolaan Produk')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <form action="{{ route('poz::transaction.product-variant.store') }}" method="POST" id="variantForm">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}" />

                {{-- Input Hidden untuk menentukan apakah pakai varian atau tidak --}}
                <input type="hidden" name="has_variant" value="{{ $tierCount > 0 ? 'yes' : 'no' }}">

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0 fw-bold text-dark">Manajemen Stock Produk: {{ $product->name }}</h5>
                            <small class="text-muted">ID Produk: {{ $product->code }}</small>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('poz::transaction.product.index') }}" class="btn btn-light btn-sm px-3">Batal</a>
                            <button type="submit" class="btn btn-primary btn-sm px-3">
                                <i class="fa fa-save me-1"></i> Simpan Data
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        @if($tierCount > 0)
                            {{-- TAMPILAN JIKA ADA VARIAN --}}
                            <div class="alert alert-light border-start border-info border-4 d-flex align-items-center py-2" role="alert">
                                <i class="fa fa-info-circle text-info me-3"></i>
                                <div>
                                    Struktur Varian: <strong>{{ $tier1->name ?? 'Pilihan 1' }}</strong>
                                    @if($tierCount == 2) <span class="mx-2 text-muted">&</span> <strong>{{ $tier2->name ?? 'Pilihan 2' }}</strong> @endif
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover align-middle" id="variantTable">
                                    <thead class="table-light">
                                        <tr class="text-uppercase small fw-bold">
                                            <th class="py-3 px-3">{{ $tier1->name ?? 'Pilihan 1' }}</th>
                                            @if($tierCount == 2) <th class="py-3">{{ $tier2->name ?? 'Pilihan 2' }}</th> @endif
                                            <th class="py-3">Kode Varian</th>
                                            <th class="py-3" style="width: 180px;">Harga Jual</th>
                                            <th class="py-3" style="width: 100px;">Stok</th>
                                            <th class="py-3" style="width: 100px;">Alert</th>
                                            <th class="py-3 text-center" style="width: 50px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            // Filter hanya yang tipe with_variant atau data lama
                                            $displayVariants = collect($savedVariants)->where('variant_type', '!=', 'no_variant')->all();
                                        @endphp

                                        @if(count($displayVariants) > 0)
                                            @foreach($displayVariants as $index => $item)
                                            <tr class="variant-row">
                                                <td class="px-3">
                                                    <select name="tier_1_ids[]" class="form-select form-select-sm t1-select border-0 bg-light" required>
                                                        <option value="">Pilih...</option>
                                                        @foreach($options1 as $opt)
                                                            <option value="{{ $opt->id }}" {{ $item['tier_1_id'] == $opt->id ? 'selected' : '' }}>{{ $opt->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                @if($tierCount == 2)
                                                <td>
                                                    <select name="tier_2_ids[]" class="form-select form-select-sm t2-select border-0 bg-light" required>
                                                        <option value="">Pilih...</option>
                                                        @foreach($options2 as $opt)
                                                            <option value="{{ $opt->id }}" {{ $item['tier_2_id'] == $opt->id ? 'selected' : '' }}>{{ $opt->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                @endif
                                                <td>
                                                    <input type="text" name="codes[]" class="form-control form-control-sm border-0 bg-light" value="{{ $item['code'] }}">
                                                </td>
                                                <td>
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text bg-light border-0">Rp</span>
                                                        <input type="number" name="prices[]" class="form-control form-control-sm border-0 bg-light" value="{{ $item['price'] }}">
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="number" name="qtys[]" class="form-control form-control-sm border-0 bg-light text-center" value="{{ $item['qty'] }}">
                                                </td>
                                                <td>
                                                    <input type="number" name="alert_qtys[]" class="form-control form-control-sm border-0 bg-light text-center" value="{{ $item['alert_qty'] }}">
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-link text-danger p-0 remove-row">
                                                        <i class="fa fa-trash-alt"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            @endforeach
                                        @else
                                            <tr class="variant-row">
                                                {{-- Baris Default Kosong --}}
                                                <td class="px-3">
                                                    <select name="tier_1_ids[]" class="form-select form-select-sm t1-select border-0 bg-light" required>
                                                        <option value="">Pilih...</option>
                                                        @foreach($options1 as $opt)
                                                            <option value="{{ $opt->id }}">{{ $opt->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                @if($tierCount == 2)
                                                <td>
                                                    <select name="tier_2_ids[]" class="form-select form-select-sm t2-select border-0 bg-light" required>
                                                        <option value="">Pilih...</option>
                                                        @foreach($options2 as $opt)
                                                            <option value="{{ $opt->id }}">{{ $opt->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                @endif
                                                <td>
                                                    <input type="text" name="codes[]" class="form-control form-control-sm border-0 bg-light" value="{{ $product->code }}-{{ rand(100,999) }}">
                                                </td>
                                                <td>
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text bg-light border-0">Rp</span>
                                                        <input type="number" name="prices[]" class="form-control form-control-sm border-0 bg-light" value="{{ (int)$product->price }}">
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="number" name="qtys[]" class="form-control form-control-sm border-0 bg-light text-center" value="0">
                                                </td>
                                                <td>
                                                    <input type="number" name="alert_qtys[]" class="form-control form-control-sm border-0 bg-light text-center" value="5">
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-link text-danger p-0 remove-row">
                                                        <i class="fa fa-trash-alt"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-grid mt-3">
                                <button type="button" class="btn btn-outline-primary btn-sm py-2 border-dashed" id="btnAddRow">
                                    <i class="fa fa-plus-circle me-1"></i> Tambah Kombinasi Varian Baru
                                </button>
                            </div>
                        @else
                            {{-- TAMPILAN JIKA TIDAK ADA VARIAN (SINGLE PRODUCT) --}}
                            <div class="alert alert-warning border-0 py-2 mb-4">
                                <i class="fa fa-exclamation-triangle me-2"></i> Produk ini tidak dikonfigurasi menggunakan varian.
                                Namun penambahan stock bisa disini
                            </div>

                            @php
                                $single = collect($savedVariants)->where('variant_type', 'no_variant')->first();
                            @endphp

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="small fw-bold text-uppercase text-muted mb-2">Kode Produk</label>
                                    <input type="text" name="product_code" class="form-control bg-light border-0" value="{{ $single['code'] ?? $product->code }}" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="small fw-bold text-uppercase text-muted mb-2">Harga Jual</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0">Rp</span>
                                        <input type="number" name="single_price" class="form-control bg-light border-0" value="{{ $single['price'] ?? (int)$product->price }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label class="small fw-bold text-uppercase text-muted mb-2">Stok Saat Ini</label>
                                    <input type="number" name="single_qty" class="form-control bg-light border-0 text-center" value="{{ $single['qty'] ?? 0 }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="small fw-bold text-uppercase text-muted mb-2">Alert Qty</label>
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
    .table > :not(caption) > * > * { padding: 0.75rem 0.5rem; }
    .form-select:focus, .form-control:focus { box-shadow: none; background-color: #f0f2f5 !important; border: 1px solid #dee2e6 !important; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tierCount = {{ (int)$tierCount }};
        if(tierCount > 0) {
            const tableBody = document.querySelector('#variantTable tbody');
            const btnAdd = document.querySelector('#btnAddRow');

            btnAdd.addEventListener('click', function() {
                const rows = tableBody.querySelectorAll('tr');
                const clone = rows[0].cloneNode(true);

                clone.querySelectorAll('select').forEach(select => select.selectedIndex = 0);
                clone.querySelectorAll('input').forEach(input => {
                    if(input.name === 'codes[]') {
                        input.value = "{{ $product->code }}-" + Math.floor(Math.random() * (999 - 100 + 1) + 100);
                    } else if(input.name === 'qtys[]') {
                        input.value = 0;
                    }
                });

                tableBody.appendChild(clone);
            });

            tableBody.addEventListener('change', function(e) {
                if (e.target.classList.contains('t1-select') || e.target.classList.contains('t2-select')) {
                    validateDuplicate(e.target);
                }
            });

            tableBody.addEventListener('click', function(e) {
                const btnRemove = e.target.closest('.remove-row');
                if (btnRemove) {
                    const rows = tableBody.querySelectorAll('tr');
                    if (rows.length > 1) {
                        btnRemove.closest('tr').remove();
                    } else {
                        alert('Minimal harus menyisakan satu baris varian.');
                    }
                }
            });

            function validateDuplicate(element) {
                const currentRow = element.closest('tr');
                const rows = Array.from(tableBody.querySelectorAll('tr'));
                const t1Val = currentRow.querySelector('.t1-select').value;
                const t2Val = tierCount === 2 ? currentRow.querySelector('.t2-select').value : null;

                if (!t1Val || (tierCount === 2 && !t2Val)) return;

                let duplicateCount = 0;
                rows.forEach(row => {
                    const rowT1 = row.querySelector('.t1-select').value;
                    const rowT2 = tierCount === 2 ? row.querySelector('.t2-select').value : null;
                    if (rowT1 === t1Val && rowT2 === t2Val) duplicateCount++;
                });

                if (duplicateCount > 1) {
                    alert('Kombinasi varian sudah ada dalam daftar.');
                    if (rows.length === 1) {
                        element.selectedIndex = 0;
                    } else {
                        currentRow.remove();
                    }
                }
            }
        }
    });
</script>
@endsection
