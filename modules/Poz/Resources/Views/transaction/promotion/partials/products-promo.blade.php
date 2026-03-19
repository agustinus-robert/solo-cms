@push('styles')
<style>
    .card { border-radius: 15px; border: none; overflow: visible !important; }
    .card-header { border-bottom: 1px solid #f0f0f0; background-color: #fff; border-top-left-radius: 15px !important; border-top-right-radius: 15px !important; }

    .form-control-lg, .select2-container--bootstrap-5 .select2-selection {
        min-height: 48px !important;
        border: 1px solid #dee2e6 !important;
        border-radius: 8px !important;
        display: flex !important;
        align-items: center !important;
    }

    .select2-container--open.select2-container--above .select2-selection {
        border-top-left-radius: 0 !important;
        border-top-right-radius: 0 !important;
        border-bottom-left-radius: 8px !important;
        border-bottom-right-radius: 8px !important;
    }

    .select2-dropdown.select2-dropdown--above {
        border-bottom: none !important;
        border-top: 1px solid #dee2e6 !important;
        border-radius: 8px 8px 0 0 !important;
        box-shadow: 0 -0.5rem 1rem rgba(0,0,0,0.1) !important;
        background: white;
        transform: translateY(2px);
    }

    .select2-container--open { z-index: 9999999 !important; }
</style>
@endpush

<div id="config-product" class="card border-0 shadow-sm mb-4" style="display:none;">
    <div class="card-header bg-primary text-white py-3">
        <h6 class="mb-0 fw-bold">Konfigurasi Per Produk</h6>
    </div>
    <div class="card-body">
        <div class="mb-3">
            <label class="form-label fw-semibold">Pilih Produk Utama</label>
            <select name="config[product_id]" class="form-select select2-promo w-100">
                <option value="">-- Cari Produk --</option>
                @foreach($products as $p)
                    <option value="{{ $p->id }}" {{ (isset($promotion->config['product_id']) && $promotion->config['product_id'] == $p->id) ? 'selected' : '' }}>
                        [{{ $p->code }}] {{ $p->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Minimal Qty Beli</label>
                <input type="number" name="config[min_qty]" class="form-control" value="{{ $promotion->config['min_qty'] ?? '1' }}">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Tipe Reward</label>
                <select name="config[reward_type]" id="reward_type" class="form-select">
                    <option value="1" {{ (isset($promotion->config['reward_type']) && $promotion->config['reward_type'] == 1) ? 'selected' : '' }}>Potongan Harga (Diskon)</option>
                    <option value="2" {{ (isset($promotion->config['reward_type']) && $promotion->config['reward_type'] == 2) ? 'selected' : '' }}>Bonus Produk (Buy X Get Y)</option>
                </select>
            </div>
        </div>

        <div class="row border-top pt-3 mt-2">
            <div class="col-md-12" id="div-discount">
                <label class="form-label fw-semibold">Nilai Potongan (Per Item)</label>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="number" name="config[reward_value]" class="form-control" value="{{ $promotion->config['reward_value'] ?? '' }}" placeholder="0">
                </div>
            </div>

            <div class="col-md-8 d-none" id="div-bonus-item">
                <label class="form-label fw-semibold">Hadiah Produk Bonus</label>
                <select name="config[bonus_product_id]" class="form-select select2-promo w-100">
                    <option value="">-- Pilih Produk Bonus --</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}" {{ (isset($promotion->config['bonus_product_id']) && $promotion->config['bonus_product_id'] == $p->id) ? 'selected' : '' }}>
                            {{ $p->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-none" id="div-bonus-qty">
                <label class="form-label fw-semibold">Qty Bonus</label>
                <input type="number" name="config[bonus_qty]" class="form-control" value="{{ $promotion->config['bonus_qty'] ?? '' }}" placeholder="0">
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        function initSelect2Promo() {
            $('.select2-promo').each(function() {
                if ($(this).data('select2')) {
                    $(this).select2('destroy');
                }

                $(this).select2({
                    theme: 'bootstrap-5',
                    placeholder: 'Pilih...',
                    width: '100%',
                    dropdownParent: $(document.body)
                }).on('select2:open', function() {
                    setTimeout(() => {
                        $('.select2-container--open').addClass('select2-container--above').removeClass('select2-container--below');
                        $('.select2-dropdown').addClass('select2-dropdown--above').removeClass('select2-dropdown--below');
                    }, 0);
                });
            });
        }

        const rewardType = document.getElementById('reward_type');
        const divDiscount = document.getElementById('div-discount');
        const divBonusItem = document.getElementById('div-bonus-item');
        const divBonusQty = document.getElementById('div-bonus-qty');

        function checkRewardDisplay() {
            if (!rewardType) return;
            const isBonus = (rewardType.value == "2");

            if (!isBonus) {
                divDiscount?.classList.remove('d-none');
                divBonusItem?.classList.add('d-none');
                divBonusQty?.classList.add('d-none');
            } else {
                divDiscount?.classList.add('d-none');
                divBonusItem?.classList.remove('d-none');
                divBonusQty?.classList.remove('d-none');
                // Beri waktu sebentar agar elemen d-none hilang baru select2 di-init
                setTimeout(initSelect2Promo, 50);
            }
        }

        if (rewardType) {
            rewardType.addEventListener('change', checkRewardDisplay);
        }

        checkRewardDisplay();
        initSelect2Promo();
    });
</script>
@endpush
