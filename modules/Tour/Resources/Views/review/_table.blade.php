<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="bg-light">
            <tr>
                <th class="ps-3" width="200">Tour</th>
                <th width="150">Rating</th>
                <th>Komentar</th>
                <th width="150">Tanggal</th>
                <th class="text-end pe-3">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reviews as $review)
            <tr>
                <td class="ps-3">
                    <span class="fw-bold text-dark">{{ $review->tour->title }}</span>
                </td>
                <td>
                    @for($i=1; $i<=5; $i++)
                        <i class="mdi mdi-star {{ $i <= $review->rating ? 'text-warning' : 'text-hint opacity-25' }}"></i>
                    @endfor
                </td>
                <td>
                    <div class="text-wrap" style="max-width: 400px;">
                        {{ $review->comment }}
                    </div>
                </td>
                <td class="text-muted small">
                    {{ $review->created_at->format('d M Y') }}
                </td>
                <td class="text-end pe-3">
                    <button type="button"
                            onclick="if(confirm('Hapus review ini?')) { document.getElementById('delete-form').action = '{{ route('tour::tour-review.destroy', $review->id) }}'; document.getElementById('delete-form').submit(); }"
                            class="btn btn-sm btn-white border text-danger shadow-sm">
                        <i class="mdi mdi-trash-can"></i>
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center py-5 text-muted">Belum ada review masuk.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="card-footer bg-white border-top-0 py-3">
    <div class="d-flex justify-content-between align-items-center">
        <small class="text-muted">Total: {{ $reviews->total() }} Review</small>
        <div>{{ $reviews->links('pagination::bootstrap-5') }}</div>
    </div>
</div>
