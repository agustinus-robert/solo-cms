<div class="container-fluid bg-light py-5" @if($canEdit) id="section-1859690864013928" @endif>
    <div class="container">
        <div class="row g-4">
            @foreach($items as $item)
                @php
                    $json = get_content_json($item);
                    $pntr = cms_encode($item->id);
                @endphp
                <div class="col-lg-6 {{ $canEdit ? '' : 'wow fadeInLeft' }} position-relative" data-wow-delay="0.2s">
                    <a href="#" class="d-flex align-items-center justify-content-between border bg-white rounded p-4">
                        <div>
                            <p class="text-muted mb-3" data-field="post0-{{ $pntr }}">{{ $json['post0'] ?? 'Find The Best Camera for You!' }}</p>
                            <h3 class="text-primary" data-field="title-{{ $pntr }}">{{ $json['title'] ?? 'Smart Camera' }}</h3>
                            <h1 class="display-3 text-secondary mb-0">
                                <span data-field="post1-{{ $pntr }}">{{ $json['post1'] ?? '40%' }}</span>
                                <span class="text-primary fw-normal" data-field="post2-{{ $pntr }}">{{ $json['post2'] ?? 'Off' }}</span>
                            </h1>
                        </div>
                        <img data-field="image-{{ $pntr }}" src="{{ asset('uploads/'.$item->location.'/'.$item->image) }}" class="img-fluid" style="max-width: 150px;" alt="">
                    </a>

                    @if($canEdit)
                        <x-live-editor
                            idMenu="1859690864013928"
                            :custom="'top: 10px; right: 25px; z-index: 1000;'"
                            :postId="encrypt($item->id)"
                            :content="$json"
                            label="edit"
                            icon="fas fa-pencil-alt"
                        />
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>
