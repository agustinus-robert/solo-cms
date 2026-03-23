<div class="tab-pane active" id="nav-about" role="tabpanel" aria-labelledby="nav-about-tab">
    @if($product->description && strip_tags($product->description) != '')
        {!! $product->description !!}
    @else
        <div class="alert alert-light border-0 text-center py-5" style="background-color: #f8f9fa;">
            <i class="bi bi-info-circle d-block mb-3" style="font-size: 2rem; color: #ced4da;"></i>
            <h5 class="text-dark">Informasi Produk</h5>
            <p class="text-muted">Maaf, deskripsi lengkap untuk produk ini sedang dalam proses pembaharuan.</p>
        </div>
    @endif
</div>
