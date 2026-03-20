<div class="table-responsive">
    <table class="table align-middle" id="productTable">
        <thead class="bg-light {{ isset($isSmall) ? 'd-none' : '' }}">
            <tr class="small text-uppercase fw-bold text-secondary">
                <th class="py-3">Produk</th>
                <th class="py-3 text-center" style="width: {{ isset($isSmall) ? '80px' : '120px' }};">Qty</th>
                @if(!isset($isSmall)) <th class="py-3 text-end">Harga</th> @endif
                <th class="py-3 text-end">Total</th>
                <th class="py-3 text-center" style="width: 40px;"></th>
            </tr>
        </thead>
        <tbody id="selectedItemsBody">
            </tbody>
    </table>
</div>
