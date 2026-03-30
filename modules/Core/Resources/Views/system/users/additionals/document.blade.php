<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0 text-primary"><i class="mdi mdi-cloud-upload me-1"></i> Unggah Dokumen Baru</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('core::system.user-document.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <input type="hidden" name="user" value="{{ $user->id }}" />

                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Nama Dokumen</label>
                            <input type="text" class="form-control" name="document_name" placeholder="Doc: KTP/KK/Surat Keterangan" />
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-bold">Pilih File</label>
                            <input type="file" name="file" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="mdi mdi-plus-circle me-1"></i> Simpan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <h6 class="fw-bold mb-0">Daftar Dokumen Terlampir</h6>
            </div>
            <div class="card-body p-0">
                @if(!empty($documents) && count($documents) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3" style="width: 50px;">#</th>
                                    <th>Nama Berkas</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($documents as $index => $doc)
                                <tr>
                                    <td class="ps-3">{{ $index + 1 }}</td>
                                    <td><small class="fw-medium text-dark">{{ $doc->name }}</small></td>
                                    <td class="text-center">
                                        <a href="{{ asset($doc->file) }}" target="_blank" class="btn btn-sm btn-outline-primary me-1">
                                            <i class="mdi mdi-download-circle-outline"></i>
                                        </a>
                                        <form action="{{ route('core::system.user-document.destroy', $doc->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus dokumen ini?')">
                                                <i class="mdi mdi-delete"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="mdi mdi-file-search-outline text-muted" style="font-size: 5rem; opacity: 0.3;"></i>
                        </div>
                        <h5 class="text-muted fw-normal">Belum ada dokumen ditemukan</h5>
                        <p class="text-muted small">Silakan unggah dokumen pendukung melalui form di atas.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>