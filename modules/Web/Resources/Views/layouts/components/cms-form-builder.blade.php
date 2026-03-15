@php
    // Jika $posting null, buat object kosong agar property access tidak error
    $posting = $posting ?? (object) ['content' => null, 'image' => null, 'location' => null];

    $content_raw = isset($posting->content) ? json_decode($posting->content, true) : [];
    $content = $content_raw['id'] ?? $content_raw;

    $hasEditor = false;
    foreach($post_codes as $pc) {
        foreach($pc as $key => $val) {
            if(strpos($key, 'fy') === 0 && $val === 'editor') {
                $hasEditor = true;
                break 2;
            }
        }
    }
@endphp

@if($hasEditor)
    <script src="{{ asset('vendor/tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>
@endif

<style>
    /* ... style tetap sama ... */
    .offcanvas.offcanvas-end { width: 600px !important; }
    .field-group {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        border-left: 4px solid #0d6efd;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .sticky-footer {
        position: sticky; bottom: 0; background: white;
        padding: 1.5rem; margin: 2rem -1.5rem -1.5rem -1.5rem;
        border-top: 1px solid #dee2e6; z-index: 10;
    }

    /* CSS UNTUK FLOATING EDITOR */
    .floating-tinymce-container {
        position: fixed; z-index: 999999;
        top: 100px; left: 50px;
        width: 600px; height: 450px;
        background: white; border-radius: 10px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.3);
        border: 1px solid #ddd;
        display: none; resize: both; overflow: hidden;
        min-width: 400px; min-height: 300px;
    }
    .floating-tinymce-header {
        background: #212529; color: white;
        padding: 8px 15px; cursor: move;
        display: flex; justify-content: space-between; align-items: center;
    }
    .floating-tinymce-body { height: calc(100% - 40px); }
    .tox-tinymce { border: none !important; height: 100% !important; }

    .vanilla-dropify {
        position: relative;
        width: 100%;
        height: 150px;
        border: 2px dashed #0d6efd;
        border-radius: 10px;
        background: #fdfdfd;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .vanilla-dropify:hover { background: #f0f7ff; }
    .vanilla-dropify input[type="file"] {
        position: absolute; width: 100%; height: 100%;
        opacity: 0; cursor: pointer; z-index: 3;
    }
    .dropify-preview {
        position: absolute; top: 0; left: 0; width: 100%; height: 100%;
        background-size: cover; background-position: center;
        display: none; z-index: 2;
    }
    .dropify-infos { text-align: center; color: #777; z-index: 1; }
    .dropify-infos i { font-size: 30px; color: #0d6efd; display: block; }
    .btn-remove-preview {
        position: absolute; top: 5px; right: 5px;
        z-index: 10; display: none; padding: 2px 10px; font-size: 12px;
    }

    #custom-alert-container {
        position: fixed; top: 20px; right: 20px; z-index: 999999;
    }
    .custom-alert {
        min-width: 280px; padding: 16px 20px; margin-bottom: 10px;
        border-radius: 8px; color: white; font-weight: 600;
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
        display: flex; align-items: center; justify-content: space-between;
        animation: slideInCustom 0.3s ease-out forwards;
    }
    .alert-success { background-color: #10b981; border-left: 6px solid #059669; }
    .alert-error { background-color: #ef4444; border-left: 6px solid #dc2626; }

    @keyframes slideInCustom {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    .fade-out-custom {
        opacity: 0; transform: translateX(100%); transition: 0.4s ease-in;
    }

    .htmx-indicator { display: none; }
    .htmx-request .htmx-indicator { display: inline-block; }
    .htmx-request .btn-content { display: none; }
</style>

<div id="custom-alert-container"></div>

<div class="p-4 animate__animated animate__fadeIn">
    <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
        <div class="d-flex align-items-center">
            <div class="bg-primary-subtle p-2 rounded me-3">
                <i class="mdi mdi-square-edit-outline text-primary fs-4"></i>
            </div>
            <div>
                <h6 class="mb-0 fw-bold">Live Editor Builder</h6>
                <small class="text-muted">ID: {{ $id_menu }}</small>
            </div>
        </div>
    </div>

    <form hx-post="{{ route('web::editor-sidebar.update', $id_menu) }}"
          hx-swap="none"
          hx-encoding="multipart/form-data"
          id="form-cms-builder">

        @csrf
        @method('PUT')
        <input type="hidden" name="post_id" value="{{ encrypt($post_id) }}">
        <input type="hidden" name="id_menu" value="{{ $id_menu }}">

        <div class="mb-4">
            <label class="form-label small fw-bolder text-uppercase text-primary">Gambar Utama / Logo</label>
            <div class="vanilla-dropify">
                <input type="file" name="image" accept="image/*" onchange="window.handleVanillaPreview(this)">
                <div class="dropify-preview"
                     style="{{ !empty($posting->image) ? 'display:block; background-image:url('.asset('uploads/'.$posting->location.'/'.$posting->image).')' : '' }}">
                </div>
                <div class="dropify-infos">
                    <i class="mdi mdi-cloud-upload-outline"></i>
                    <span class="small">Klik atau seret gambar</span>
                </div>
                <button type="button" class="btn btn-danger btn-sm btn-remove-preview"
                        onclick="window.removeVanillaPreview(this)"
                        style="{{ !empty($posting->image) ? 'display:block' : '' }}">Hapus</button>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label small fw-bolder text-uppercase">Judul Utama (Title)</label>
            <input type="text" name="title" class="form-control form-control-lg"
                   value="{{ $content['title'] ?? '' }}"
                   placeholder="Masukkan judul..."
                   oninput="window.updateLivePreview('{{ $id_menu }}', 'title', this.value)">
        </div>

        <div class="separator mb-4 border-top"></div>

        @php $i = 0; @endphp
        @foreach($post_codes as $index_pc => $value_pc)
            @php
                $replace_field = str_replace("field_", "", $index_pc);
                $name = "post" . $i++;
                $type = $value_pc['fy' . $replace_field];
                $label = $value_pc['ft' . $replace_field]['id'] ?? 'Field ' . $i;
                $current_val = $content[$name] ?? '';
            @endphp

            <div class="mb-4 field-group">
                <label class="form-label small fw-bolder text-uppercase text-primary mb-2 d-block">{{ $label }}</label>

                @if($type == 'raw_text')
                    <input type="text" name="{{ $name }}" class="form-control" value="{{ $current_val }}"
                           oninput="window.updateLivePreview('{{ $id_menu }}', '{{ $name }}', this.value)">

                @elseif($type == 'textarea')
                    <textarea name="{{ $name }}" class="form-control" rows="5"
                              oninput="window.updateLivePreview('{{ $id_menu }}', '{{ $name }}', this.value)">{{ $current_val }}</textarea>

                @elseif($type == 'editor')
                    <button type="button" class="btn btn-dark btn-sm w-100 mb-2" onclick="window.toggleFloatingEditor('{{ $name }}')">
                        <i class="mdi mdi-unfold-more-vertical me-1"></i> Buka Editor Jendela Terpisah
                    </button>

                    <div class="editor-container shadow-sm d-none">
                        <textarea id="editor-{{ $name }}" name="{{ $name }}"
                                  class="form-control tiny-editor"
                                  data-field="{{ $name }}">{!! $current_val !!}</textarea>
                    </div>
                    <div id="mini-preview-{{ $name }}" class="small text-muted text-truncate border-top pt-1">
                        {!! strip_tags($current_val) ?: 'Konten kosong...' !!}
                    </div>

                @elseif($type == 'currency')
                    <div class="input-group">
                        <span class="input-group-text bg-white">Rp</span>
                        <input type="number" name="{{ $name }}" class="form-control" value="{{ $current_val }}"
                               oninput="window.updateLivePreview('{{ $id_menu }}', '{{ $name }}', this.value)">
                    </div>

                @elseif($type == 'date')
                    <input type="date" name="{{ $name }}" class="form-control" value="{{ $current_val }}"
                           oninput="window.updateLivePreview('{{ $id_menu }}', '{{ $name }}', this.value)">
                @endif
            </div>
        @endforeach

        <div class="sticky-footer shadow-lg">
            <button type="submit" class="btn btn-primary w-100 mb-2 py-2 fw-bold">
                <span class="btn-content">
                    <i class="mdi mdi-content-save-outline me-1"></i> Simpan Perubahan
                </span>
                <span class="htmx-indicator">
                    <span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...
                </span>
            </button>
        </div>
    </form>
</div>

<script>
    // --- 1. LOGIKA FLOATING EDITOR ---
    window.toggleFloatingEditor = function(name) {
        let containerId = 'float-container-' + name;
        let el = document.getElementById(containerId);

        if (!el) {
            el = document.createElement('div');
            el.id = containerId;
            el.className = 'floating-tinymce-container';
            el.innerHTML = `
                <div class="floating-tinymce-header">
                    <span class="small fw-bold"><i class="mdi mdi-cursor-move me-1"></i> Floating Editor</span>
                    <button type="button" class="btn-close btn-close-white" style="font-size:10px" onclick="this.parentElement.parentElement.style.display='none'"></button>
                </div>
                <div class="floating-tinymce-body">
                    <textarea id="temp-editor-${name}"></textarea>
                </div>
            `;
            document.body.appendChild(el);

            // Set value awal dari textarea asli
            const initialVal = document.getElementById('editor-' + name).value;
            document.getElementById('temp-editor-' + name).value = initialVal;

            // Init TinyMCE di floating window
            tinymce.init({
                selector: `#temp-editor-${name}`,
                height: '100%',
                menubar: false,
                plugins: 'lists link code autoresize table',
                toolbar: 'bold italic underline | bullist numlist | table link | code',
                setup: function(editor) {
                    editor.on('input Change keyup NodeChange', function() {
                        const content = editor.getContent();
                        // Update textarea asli di sidebar
                        document.getElementById('editor-' + name).value = content;
                        // Update preview teks di sidebar
                        const preview = document.getElementById('mini-preview-' + name);
                        if(preview) preview.innerText = editor.getContent({format:'text'}).substring(0, 40) + '...';
                        // Trigger Live Preview
                        const updateFunc = window.updateLivePreview || window.parent.updateLivePreview;
                        if (typeof updateFunc === 'function') {
                            updateFunc('{{ $id_menu }}', name, content);
                        }
                    });
                }
            });
            initDraggable(el);
        }
        el.style.display = (el.style.display === 'none' || el.style.display === '') ? 'block' : 'none';
    };

    function initDraggable(el) {
        const header = el.querySelector(".floating-tinymce-header");
        let p1 = 0, p2 = 0, p3 = 0, p4 = 0;
        header.onmousedown = (e) => {
            e.preventDefault();
            p3 = e.clientX; p4 = e.clientY;
            document.onmouseup = () => { document.onmouseup = null; document.onmousemove = null; };
            document.onmousemove = (e) => {
                e.preventDefault();
                p1 = p3 - e.clientX; p2 = p4 - e.clientY;
                p3 = e.clientX; p4 = e.clientY;
                el.style.top = (el.offsetTop - p2) + "px";
                el.style.left = (el.offsetLeft - p1) + "px";
            };
        };
    }

    // --- 2. LOGIKA PREVIEW GAMBAR ---
    window.handleVanillaPreview = function(input) {
        const parent = input.parentElement;
        const preview = parent.querySelector('.dropify-preview');
        const removeBtn = parent.querySelector('.btn-remove-preview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.style.backgroundImage = `url(${e.target.result})`;
                preview.style.display = 'block';
                removeBtn.style.display = 'block';
                const updateFunc = window.updateLivePreview || window.parent.updateLivePreview;
                if (typeof updateFunc === 'function') updateFunc('{{ $id_menu }}', 'image', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    };

    window.handleVanillaPreview = function(input) {
        const parent = input.parentElement;
        const preview = parent.querySelector('.dropify-preview');
        const removeBtn = parent.querySelector('.btn-remove-preview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.style.backgroundImage = `url(${e.target.result})`;
                preview.style.display = 'block';
                removeBtn.style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        }
    };

    window.handleVanillaPreview = function(input) {
        const parent = input.parentElement;
        const preview = parent.querySelector('.dropify-preview');
        const removeBtn = parent.querySelector('.btn-remove-preview');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.style.backgroundImage = `url(${e.target.result})`;
                preview.style.display = 'block';
                removeBtn.style.display = 'block';

                const idMenu = '{{ $id_menu }}';
                const updateFunc = window.updateLivePreview || window.parent.updateLivePreview;

                if (typeof updateFunc === 'function') {
                    updateFunc(idMenu, 'image', e.target.result);
                    console.log("Trigger live preview gambar berhasil dikirim");
                } else {
                    console.error("Fungsi updateLivePreview tidak ditemukan di parent window!");
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    };

    (function() {
        window.showMyAlert = function(msg, type = 'success') {
            const container = document.getElementById('custom-alert-container');
            if (!container) return;

            container.innerHTML = '';

            const el = document.createElement('div');
            el.className = `custom-alert alert-${type}`;
            el.innerHTML = `<span>${msg}</span><i class="mdi mdi-close ms-3" style="cursor:pointer" onclick="this.parentElement.remove()"></i>`;
            container.appendChild(el);

            setTimeout(() => {
                if(el.parentNode) {
                    el.classList.add('fade-out-custom');
                    setTimeout(() => el.remove(), 400);
                }
            }, 2500);
        };

        if (!window.parent._hasCmsHandler) {
            window.parent._isProcessingCMS = false;

            window.parent._handleCmsHTMX = function(evt) {
                if (evt.detail.elt && evt.detail.elt.id === 'form-cms-builder') {

                    if (evt.detail.successful) {
                        if (window.parent._isProcessingCMS) return;
                        window.parent._isProcessingCMS = true;

                        if (typeof window.showMyAlert === 'function') {
                            window.showMyAlert('Konten Berhasil Disimpan!');
                        }

                        const form = evt.detail.elt;
                        const sectionId = form.querySelector('[name="id_menu"]').value;
                        const selector = `#section-${sectionId}`;
                        const currentUrl = window.parent.location.href;

                        fetch(currentUrl)
                            .then(response => response.text())
                            .then(html => {
                                const parser = new DOMParser();
                                const doc = parser.parseFromString(html, 'text/html');
                                const newSection = doc.querySelector(selector);
                                const oldSection = window.parent.document.querySelector(selector);

                                console.log("Mencari selector:", selector);
                                console.log("Ketemu section baru?", newSection ? 'YA' : 'TIDAK');
                                console.log("Ketemu section lama?", oldSection ? 'YA' : 'TIDAK');

                                if (newSection && oldSection) {
                                    oldSection.innerHTML = newSection.innerHTML;
                                    if (window.parent.htmx) window.parent.htmx.process(oldSection);

                                    if (window.parent.tns) {
                                        const container = oldSection.querySelector('.tiny-slider-inner');
                                        if (container) {
                                            window.parent.tns({
                                                container: container,
                                                autoplay: container.dataset.autoplay === 'true',
                                                autoplayButtonOutput: false,
                                                autoplayHoverPause: true,
                                                gutter: parseInt(container.dataset.gutter) || 0,
                                                controls: container.dataset.arrow === 'true',
                                                nav: container.dataset.dots === 'true',
                                                items: parseInt(container.dataset.items) || 5,
                                                mouseDrag: true,
                                                controlsText: ['<i class="fas fa-chevron-left"></i>', '<i class="fas fa-chevron-right"></i>'],
                                                responsive: {
                                                    0: { items: parseInt(container.dataset.itemsXs) || 1 },
                                                    768: { items: parseInt(container.dataset.itemsMd) || 2 },
                                                    992: { items: parseInt(container.dataset.itemsLg) || 3 },
                                                    1200: { items: parseInt(container.dataset.items) || 5 }
                                                }
                                            });
                                        }
                                    }
                                    window.parent.dispatchEvent(new Event('resize'));
                                }
                                window.parent._isProcessingCMS = false;
                            })
                            .catch(err => {
                                console.error(err);
                                window.parent._isProcessingCMS = false;
                            });
                    } else {
                        if (typeof window.showMyAlert === 'function') {
                            window.showMyAlert('Gagal Menyimpan!', 'error');
                        }
                    }
                }
            };

            window.parent.document.body.addEventListener('htmx:afterRequest', window.parent._handleCmsHTMX);
            window.parent._hasCmsHandler = true;
        }
    })();
</script>
