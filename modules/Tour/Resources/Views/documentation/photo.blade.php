@extends('tour::layouts.default')

@section('navtitle')
    Galeri Foto: {{ $tour->title }}
@endsection

@section('content')
<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form action="{{ route('tour::photo.store', $tour->id) }}" method="POST" enctype="multipart/form-data" class="row align-items-center">
                    @csrf
                    <div class="col-md-9">
                        <label class="form-label fw-bold">Unggah Foto Baru</label>
                        <input type="file" name="photos[]" class="form-control" multiple accept="image/*" required>
                        <small class="text-muted">Bisa pilih banyak foto sekaligus (Format: JPG, PNG. Max: 2MB per file).</small>
                    </div>

                    <div class="col-md-3 mt-3 mt-md-0 text-md-end">
                        <button type="submit" class="btn btn-primary w-100 py-1">
                            <i class="mdi mdi-upload me-1"></i> Mulai Unggah
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="row g-3">
            @forelse($photos as $photo)
            <div class="col-md-3">
                <div class="card h-100 border-0 shadow-sm overflow-hidden position-relative group">
                    <img src="{{ asset($photo->image_path) }}"
                        class="card-img-top"
                        style="height: 200px; object-fit: cover;"
                        alt="Tour Photo">

                    @if($photo->is_primary)
                        <div class="position-absolute top-0 start-0 m-2">
                            <span class="badge bg-success shadow">Cover Utama</span>
                        </div>
                    @endif

                    <div class="card-body p-2 bg-white border-top">
                        <div class="d-flex justify-content-between align-items-center">
                            @if(!$photo->is_primary)
                                <form action="{{ route('tour::photo.primary', [$tour->id, $photo->id]) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-primary" title="Jadikan Cover">
                                        <i class="mdi mdi-star-outline"></i> Set Utama
                                    </button>
                                </form>
                            @else
                                <span class="badge bg-light text-success border">
                                    <i class="mdi mdi-check-decagram me-1"></i> Utama
                                </span>
                            @endif

                            {{-- Button Delete --}}
                            <form action="{{ route('tour::photo.destroy', $photo->id) }}" method="POST" onsubmit="return confirm('Hapus foto ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light border text-danger">
                                    <i class="mdi mdi-trash-can-outline"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm py-5 text-center">
                    <i class="mdi mdi-image-multiple-outline fs-1 text-muted"></i>
                    <p class="text-muted mt-2">Belum ada foto. Silakan unggah foto untuk mempercantik tampilan tour.</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="{{ route('tour::booking.index') }}" class="btn btn-link text-muted p-0 text-decoration-none">
        <i class="mdi mdi-arrow-left"></i> Kembali ke Daftar Tour
    </a>
</div>
@endsection
