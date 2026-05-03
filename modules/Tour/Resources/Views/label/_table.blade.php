<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="bg-light">
            <tr>
                <th class="ps-3">Preview</th>
                <th>Nama Fasilitas</th>
                <th>Slug</th>
                <th class="text-end pe-3">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($labels as $label)
            <tr>
                <td class="ps-3">
                    <span class="py-2 px-3 d-inline-flex align-items-center"
                          style="background-color: {{ $label->color }}20; color: {{ $label->color }}; border: 1px solid {{ $label->color }}">
                        <i class="mdi {{ $label->icon ?? 'mdi-tag' }} me-2"></i>
                        {{ $label->name }}
                    </span>
                </td>
                <td><span class="fw-bold">{{ $label->name }}</span></td>
                <td><code class="text-muted">{{ $label->slug }}</code></td>
                <td class="text-end pe-3">
                    <div class="btn-group shadow-sm">
                        <a href="{{ route('tour::label.edit', $label->id) }}" class="btn btn-sm btn-white border">
                            <i class="mdi mdi-pencil text-warning"></i>
                        </a>
                        <button type="button" onclick="deleteAction('{{ route('tour::label.destroy', $label->id) }}')" class="btn btn-sm btn-white border text-danger">
                            <i class="mdi mdi-trash-can"></i>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center py-5 text-muted">
                    <i class="mdi mdi-tag-off-outline d-block mb-2 opacity-25" style="font-size: 3rem;"></i>
                    Belum ada label fasilitas.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="card-footer bg-white border-top-0 py-3">
    <div class="d-flex justify-content-between align-items-center">
        <small class="text-muted">Total: {{ $labels->total() }} Label</small>
        <div>
            {{ $labels->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
