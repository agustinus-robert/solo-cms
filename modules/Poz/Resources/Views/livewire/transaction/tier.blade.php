<div class="container-fluid py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">Tier</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size: 0.8rem;">
                    <li class="breadcrumb-item"><a href="#" class="text-muted text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active">{{ $action }} Tier</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <h5 class="card-title mb-4 fw-bold">{{ $action }} Tier</h5>

            <form wire:submit="save" enctype="multipart/form-data">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label small fw-bold text-secondary">Nama Tier</label>
                        <input type="text" class="form-control @error('form.name') is-invalid @enderror" wire:model="form.name" placeholder="Masukkan nama tier...">
                        @error('form.name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-secondary">Tier Kategori</label>
                        <select class="form-select @error('form.ref_tier_id') is-invalid @enderror" wire:model="form.ref_tier_id">
                            <option value="">Pilih</option>
                            @foreach($tiers as $tier)
                                <option value="{{ $tier->id }}">{{ $tier->name }}</option>
                            @endforeach
                        </select>
                        @error('form.type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr class="my-4 text-light">

                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light px-4">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <span wire:loading.remove wire:target="save">Save Changes</span>
                        <span wire:loading wire:target="save">Saving...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
