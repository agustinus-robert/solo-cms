<div>
    <div class="container mt-4 mb-4">
    <!-- Page Title & Breadcrumb -->
        <div class="d-flex flex-column flex-md-row align-items-start justify-content-between mb-3">
            <div>
                <!-- Title -->
                <h1 class="h3 mb-2">Posting Of Data</h1>

                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="index.html">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Posting Of Data</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>


    <div class="container my-4">
        <div class="row">
            <!-- Left Sidebar -->
            <div class="col-lg-2 mb-4">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Pilihan Bahasa</h5>
                        <span class="badge bg-success rounded-circle" style="width: 15px; height: 15px;"></span>
                    </div>
                    <div class="card-body">
                        <select disabled disabled wire:change="selectlang($event.target.value)" class="form-select">
                            <option value="id">Indonesia</option>
                            <option value="en">English</option>
                        </select>

                        <input type="hidden" id="langs" wire:model.lazy="language" value="id">
                    </div>
                </div>

                @if(count($manyArr) > 0)
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Form Tambahan</h5>
                        <span class="badge bg-success rounded-circle" style="width: 15px; height: 15px;"></span>
                    </div>
                    <div class="card-body">
                        <select wire:change="selectForm($event.target.value)" wire:model.lazy="selectFormAdd" class="form-select">
                            <option value="">Pilih Form</option>
                            @foreach($allForm as $key => $val)
                                @if(isset($this->manyArr[$val->menu_id]))
                                    <option value="{{$val->id}}">{{$val->title}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif

                <?php
                foreach($data as $index => $value){
                    if($index == 'taxonomy_code'){
                        $i = 0;
                        foreach(json_decode($value, true) as $index_pc => $value_pc){
                            $replace_field = str_replace("fieldtaxo_","", $index_pc);
                            $name = "category.post".$i++;
                            $label_value = $value_pc['ft_taxo'.$replace_field];
                            $taxo_cat = $value_pc['fy_taxo'.$replace_field]; ?>

                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"><?=$label_value?></h5>
                                    <span class="badge bg-success rounded-circle" style="width: 15px; height: 15px;"></span>
                                </div>
                                <div class="card-body">
                                    <select class="form-select" wire:model="<?=$name?>">
                                        <option value="">Silahkan Pilih <?=$label_value?></option>
                                        <?php foreach(category_data($taxo_cat) as $index_dt => $value_dt){ ?>
                                            <option value="<?=$value_dt->id?>"><?=$value_dt->title?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                <?php }
                    }
                }
                ?>
            </div>

            <!-- Right Form Section -->
            <div class="col-lg-10">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">General</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label"><strong><?=($this->language == 'id' ? 'Judul' : 'Title')?></strong></label>
                            <input wire:change="helperlanguage($event.target.value)" class="form-control" type="text" wire:model.lazy="content.title" />
                        </div>
                        <?php
                        
                            if($data->post_code){
                                $i = 0;
                                foreach(json_decode($data->post_code, true) as $index_pc => $value_pc){
                                    $replace_field = str_replace("field_","", $index_pc);
                                    $name = "content.post".$i++;
                                    $label_value = $value_pc['ft'.$replace_field]; ?>

                                    <div class="mb-3">
                                        <label class="form-label"><strong><?=$label_value[$this->language] ?? $label_value[$_COOKIE['k_language']] ?? ''?></strong></label>
                                        <?php if($value_pc['fy'.$replace_field] == 'raw_text'){ ?>
                                            <input wire:change="helperlanguage($event.target.value)" wire:key="<?=$name?>"  type="text" class="form-control" wire:model.lazy="<?=$name?>" />
                                        <?php } elseif($value_pc['fy'.$replace_field] == 'textarea'){ ?>
                                            <textarea wire:change="helperlanguage($event.target.value)" wire:key="<?=$name?>" class="form-control" wire:model.lazy="<?=$name?>"></textarea>
                                        <?php } else if($value_pc['fy'.$replace_field] == 'editor'){ ?>
                                            <div wire:ignore>
                                                <textarea id="{{str_replace('content.', 'mce', $name)}}" class="form-control"></textarea>
                                            </div>
                                        <?php } elseif($value_pc['fy'.$replace_field] == 'currency'){ ?>
                                            <input wire:key="<?=$name?>" wire:change="moneys($event.target.value, '{{$name}}')" type="text" class="form-control" wire:model.lazy="<?=$name?>" />
                                        <?php } elseif($value_pc['fy'.$replace_field] == 'date'){ ?>
                                            <input wire:key="<?=$name?>" type="date" class="form-control" wire:model.lazy="<?=$name?>" />
                                        <?php } ?>
                                    </div>
                        <?php }
                            }
                        ?>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Media 
                            @if($postId == 30 || $postId == 31 || $postId == 34)
                               <p style="font-size:13px;">gambar 1280 x 500 (Landscape)</p>
                            @elseif($postId == 32)
                                <p style="font-size:13px;">gambar 920 x 1080 (Potrait)</p>
                            @endif

                            @if(!empty($medias->size_image) && !empty($medias->orientation_image))
                               <p style="font-size:13px;"> gambar {{ $medias->size_image }} ({{ $medias->orientation_image }})</p>
                            @endif
                        </h5>
                    </div>
                    <div class="card-body" wire:ignore>
                        <input type="file" class="form-control dropify" name="dropify_data" data-default-file="{{!empty($data_id) ? asset(Storage::disk('public')->url($sh_photo)) : ''}}" accept="image/*" />
                        <div class="form-text">Set your media here.</div>
                    </div>
                </div>

                {{-- <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Media Description</h5>
                    </div>
                    <div class="card-body">
                        <textarea class="form-control mb-2" wire:change="helperlanguage($event.target.value)" wire:model.lazy="content.media_description"></textarea>
                        <div class="form-text">Set your description of your media here.</div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Meta Description</h5>
                    </div>
                    <div class="card-body">
                        <textarea class="form-control mb-2" wire:change="helperlanguage($event.target.value)" wire:model="content.meta_description"></textarea>
                        <div class="form-text">Masukkan meta description</div>
                    </div>
                </div> --}}
            </div>
        </div>
    </div>


    <div class="d-flex justify-content-end mt-8">
        <button wire:click="submitForm" id="kt_ecommerce_add_product_submit" class="btn btn-primary">
            <span class="indicator-label">Save Changes</span>
        </button>
    </div>
</div>

@push('scripts')
<script src="{{ asset('vendor/tinymce/tinymce.min.js') }}"></script>

<script>
window.cookieStorage = {
    getItem(key) {
        return document.cookie
            .split(';')
            .map(c => c.trim().split('='))
            .find(c => c[0] === key)?.[1] ?? null;
    },
    setItem(key, value) {
        document.cookie = `${key}=${value}; path=/; max-age=10000`;
    }
};

$(document).ready(function() {
    $('.dropify').dropify({
        messages: {
            default: 'Upload File',
            replace: 'Replace File',
            remove: 'Remove File',
            error: 'Error File'
        }
    });
});

$(document).on('change', '.dropify', function(event) {
    @this.upload('dropify', event.target.files[0]);
});
</script>

@if($data->post_code)
    @php $i = 0; @endphp
    @foreach(json_decode($data->post_code, true) as $index_pc => $value_pc)
        @php
            $replace_field = str_replace("field_", "", $index_pc);
            $name = "content.post" . $i;
            $editorId = str_replace('content.', 'mce', $name);
            $type = $value_pc['fy' . $replace_field];
            $label_value = $value_pc['ft' . $replace_field];
            $contentValue = $content['post' . $i] ?? '';
            $i++;
        @endphp

        <div class="mb-3">
            <label class="form-label">
                <strong>{{ $label_value[$this->language] ?? $label_value[$_COOKIE['k_language']] ?? '' }}</strong>
            </label>

            @if($type === 'editor')
                @push('scripts')
                    <script>
                        document.addEventListener("DOMContentLoaded", function () {
                            if (tinymce.get("{{ $editorId }}")) {
                                tinymce.get("{{ $editorId }}").remove();
                            }

                            tinymce.init({
                                selector: '#{{ $editorId }}',
                                plugins: 'link image media code',
                                toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | outdent indent | code',
                                valid_elements: '*[*]',
                                extended_valid_elements: 'a[*],div[*],ul[*],li[*],i[*],p[*],span[*]',
                                valid_children: '+a[div|i|span|strong|em]',
                                setup: function (editor) {
                                    editor.on('init', function () {
                                        editor.setContent(`{!! addslashes($contentValue) !!}`);
                                    });

                                    editor.on('change keyup blur', function () {
                                        @this.set('{{ $name }}', editor.getContent());
                                    });
                                }
                            });
                        });
                    </script>
                @endpush
            @endif
        </div>
    @endforeach
@endif



<script>
$wire.on('helper', event => {
    for (const key in event[0]) {
        const editor = tinymce.get('mcepost' + key);
        if (editor) editor.setContent(event[0][key]);
    }
});
</script>
@endpush
