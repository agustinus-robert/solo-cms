<div class="container-fluid px-0">
    <div class="row g-0">
        @foreach($items as $item)
            @php
                $json_item = get_content_json($item);
                $pntr_item = cms_encode($item->id);
            @endphp
            <div class="col-6 col-md-4 col-lg-2 border-start border-end wow fadeInUp position-relative" data-wow-delay="0.1s">
                <div class="p-4">
                    <div class="d-inline-flex align-items-center">
                        <i class="{{ $json_item['post1'] ?? 'fa fa-sync-alt' }} fa-2x text-primary" data-field="icon-{{ $pntr_item }}"></i>

                        <div class="ms-4">
                            <h6 class="text-uppercase mb-2" data-field="title-{{ $pntr_item }}">
                                {{ $json_item['title'] ?? 'Free Return' }}
                            </h6>
                            <p class="mb-0" data-field="post0-{{ $pntr_item }}">
                                {{ $json_item['post0'] ?? '30 days money back guarantee!' }}
                            </p>
                        </div>
                    </div>
                </div>

                @if($canEdit)
                    <x-live-editor
                        idMenu="1859690724124090"
                        :custom="'top: 5px; right: 5px; z-index: 1000;'"
                        :postId="encrypt($item->id)"
                        :content="$json_item"
                        label="edit"
                        icon="fas fa-pencil-alt"
                    />
                @endif
            </div>
        @endforeach
    </div>
</div>
