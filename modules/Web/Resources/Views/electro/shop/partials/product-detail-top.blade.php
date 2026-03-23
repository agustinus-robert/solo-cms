<div class="container-fluid shop py-5">
    <div class="container py-5">
        <div class="row g-4">
            <div class="col-lg-5 col-xl-3 wow fadeInUp" data-wow-delay="0.1s">
                <div class="input-group w-100 mx-auto d-flex mb-4">
                    <input type="search" class="form-control p-3" placeholder="keywords"
                        aria-describedby="search-icon-1">
                    <span id="search-icon-1" class="input-group-text p-3"><i class="fa fa-search"></i></span>
                </div>
                @include('web::electro.shop.partials.parts.additional-product')
                @include('web::electro.shop.partials.parts.featured-product')
            </div>
            @include('web::electro.shop.partials.parts.showed-product')
        </div>
    </div>
</div>
