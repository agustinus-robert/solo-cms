<div class="container-fluid carousel bg-light px-0">
    <div class="row g-0 justify-content-end">
        <div class="col-12 col-lg-7 col-xl-9" @if($canEdit) id="section-1859690265115931" @endif>
            <div class="header-carousel owl-carousel bg-light py-5"
                data-autoplay="{{ $canEdit ? 'false' : 'true' }}"
                data-loop="{{ $canEdit ? 'false' : 'true' }}"
                data-mousedrag="{{ $canEdit ? 'false' : 'true' }}">

                @foreach($carousel as $crs)
                    @php
                        $jsoncarousel = get_content_json($crs);
                        $pntr_carousel = cms_encode($crs->id);
                    @endphp

                    {{-- Bungkus satu item carousel agar editor menempel di sini --}}
                    <div class="header-carousel-item-wrapper position-relative">
                        <div class="row g-0 header-carousel-item align-items-center">
                            <div class="col-xl-6 carousel-img wow fadeInLeft" data-wow-delay="0.1s">
                                <img data-field="image-{{ $pntr_carousel }}" src="{{ asset('uploads/'.$crs->location.'/'.$crs->image) }}" class="img-fluid w-100" alt="Image">
                            </div>
                            <div class="col-xl-6 carousel-content p-4">
                                <h4 data-field="post0-{{ $pntr_carousel }}" class="text-uppercase fw-bold mb-4" style="letter-spacing: 3px;">{{ $jsoncarousel['post0'] }}</h4>
                                <h1 data-field="post1-{{ $pntr_carousel }}" class="display-3 text-capitalize mb-4">{{ $jsoncarousel['post1'] }}</h1>
                                <p class="text-dark">Terms and Condition Apply</p>
                                <a data-field="post2-{{ $pntr_carousel }}" class="btn btn-primary rounded-pill py-3 px-5" href="{{ $jsoncarousel['post2'] }}">Shop Now</a>
                            </div>
                        </div>

                        {{-- Editor pindah ke dalam wrapper item --}}
                        @if($canEdit)
                            <x-live-editor
                                idMenu="1859690265115931"
                                :custom="'top: 10px; right: 10px; z-index: 1000;'"
                                :postId="encrypt($crs->id)"
                                :content="$jsoncarousel"
                                label="edit"
                                icon="fas fa-pencil-alt"
                            />
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <div class="col-12 col-lg-5 col-xl-3" @if($canEdit) id="section-1859690530369920" @endif>
            @foreach($offers as $offer)
                @php
                    $jsonoffer = get_content_json($offer);
                    $pntr_offer = cms_encode($offer->id);
                @endphp
                <div class="carousel-header-banner h-100 position-relative">
                    <img data-field="image-{{ $pntr_offer }}" src="{{ asset('uploads/'.$offer->location.'/'.$offer->image) }}" class="img-fluid w-100 h-100" style="object-fit: cover;" alt="Image">

                    <div class="carousel-banner-offer">
                        <p class="bg-primary text-white rounded fs-5 py-2 px-4 mb-0 me-3" data-field="post2-{{ $pntr_offer }}">{{ $jsonoffer['post2'] }}</p>
                        <p class="text-primary fs-5 fw-bold mb-0">Special Offer</p>
                    </div>

                    <div class="carousel-banner">
                        <div class="carousel-banner-content text-center p-4">
                            <a href="#" class="d-block mb-2" data-field="post3-{{ $pntr_offer }}">{{ $jsonoffer['post3'] }}</a>
                            <del class="me-2 text-white fs-5" data-field="post0-{{ $pntr_offer }}">{{ cleanRupiah($jsonoffer['post0']) }}</del>
                            <span class="text-primary fs-5" data-field="post1-{{ $pntr_offer }}">{{ cleanRupiah($jsonoffer['post1']) }}</span>
                        </div>
                    </div>

                    @if($canEdit)
                        <x-live-editor
                            idMenu="1859690530369920"
                            :custom="'top: 10px; right: 10px; z-index: 1000;'"
                            :postId="encrypt($offer->id)"
                            :content="$jsonoffer"
                            label="edit"
                            icon="fas fa-pencil-alt"
                        />
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>
