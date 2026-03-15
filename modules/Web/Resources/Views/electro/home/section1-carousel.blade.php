<div class="container-fluid carousel bg-light px-0">
    <div class="row g-0 justify-content-end">
        <div class="col-12 col-lg-7 col-xl-9">
            <div class="header-carousel owl-carousel bg-light py-5">
                @foreach($carousel as $crs)
                    @php
                        $jsoncarousel = get_content_json($crs);
                    @endphp
                    <div class="row g-0 header-carousel-item align-items-center">
                        <div class="col-xl-6 carousel-img wow fadeInLeft" data-wow-delay="0.1s">
                            <img src="{{ asset('uploads/'.$crs->location.'/'.$crs->image) }}" class="img-fluid w-100" alt="Image">
                        </div>
                        <div class="col-xl-6 carousel-content p-4">
                            <h4 class="text-uppercase fw-bold mb-4 wow fadeInRight" data-wow-delay="0.1s"
                                style="letter-spacing: 3px;">{{ $jsoncarousel['post0'] }}</h4>
                            <h1 class="display-3 text-capitalize mb-4 wow fadeInRight" data-wow-delay="0.3s">{{ $jsoncarousel['post1'] }}</h1>
                            <p class="text-dark wow fadeInRight" data-wow-delay="0.5s">Terms and Condition Apply</p>
                            <a class="btn btn-primary rounded-pill py-3 px-5 wow fadeInRight" data-wow-delay="0.7s"
                                href="{{ $jsoncarousel['post2'] }}">Shop Now</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="col-12 col-lg-5 col-xl-3 wow fadeInRight" data-wow-delay="0.1s">
            @foreach($offers as $offer)
                @php
                    $jsonoffer = get_content_json($offer);
                @endphp
                <div class="carousel-header-banner h-100">
                    <img src="{{ asset('uploads/'.$offer->location.'/'.$offer->image) }}" class="img-fluid w-100 h-100" style="object-fit: cover;" alt="Image">
                    <div class="carousel-banner-offer">
                        <p class="bg-primary text-white rounded fs-5 py-2 px-4 mb-0 me-3">{{ $jsonoffer['post2'] }}</p>
                        <p class="text-primary fs-5 fw-bold mb-0">Special Offer</p>
                    </div>
                    <div class="carousel-banner">
                        <div class="carousel-banner-content text-center p-4">
                            <a href="#" class="d-block mb-2">{{ $jsonoffer['post3'] }}</a>
                            <a href="#" class="d-block text-white fs-3">{{ $jsonoffer['post3'] }}</a>
                            <del class="me-2 text-white fs-5">{{ cleanRupiah($jsonoffer['post0']) }}</del>
                            <span class="text-primary fs-5">{{ cleanRupiah($jsonoffer['post1']) }}</span>
                        </div>
                        <a href="#" class="btn btn-primary rounded-pill py-2 px-4"><i
                                class="fas fa-shopping-cart me-2"></i> Add To Cart</a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
