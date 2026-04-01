@extends('core::layouts.default')

@section('title', 'Jabatan | ')
@section('navtitle', 'Jabatan')

@section('content')
<div class="d-flex align-items-center mb-4">
    <a class="text-decoration-none" href="{{ request('next', route('core::company.positions.index')) }}"><i class="mdi mdi-arrow-left-circle-outline mdi-36px"></i></a>
    <div class="ms-4">
        <h2 class="mb-1">{{ $position->id ? 'Ubah jabatan' : 'Buat jabatan baru' }}</h2>
        <div class="text-secondary small">
            @if($position->id)
                Silakan isi formulir di bawah ini untuk memperbarui informasi jabatan {{ $position->name }}
            @else
                Silakan isi formulir di bawah ini untuk membuat jabatan baru
            @endif
        </div>
    </div>
</div>

<div class="card mb-4 border-0">
    <div class="card-body">
        <form class="form-block" action="{{ $position->id ? route('core::company.positions.update', ['position' => $position->id, 'next' => request('next')]) : route('core::company.positions.store', ['next' => request('next')]) }}" method="POST">
            @csrf
            @if($position->id) @method('PUT') @endif

            <div class="row justify-content-center">
                <div class="col-xxl-5 col-xl-6">
                    <div class="mb-3 row">
                        <label class="col-lg-4 col-xl-3 col-form-label">Departemen</label>
                        <div class="col-xl-8 col-xxl-6">
                            <select class="form-select @error('dept_id') is-invalid @enderror" name="dept_id">
                                <option value="">-- Pilih --</option>
                                @foreach($departments as $_department)
                                    <option value="{{ $_department->id }}" @selected(old('dept_id', $position->dept_id) == $_department->id)>{{ $_department->name }}</option>
                                @endforeach
                            </select>
                            @error('dept_id')
                                <small class="text-danger d-block"> {{ $message }} </small>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3 row required">
                        <label class="col-lg-4 col-xl-3 col-form-label">Tipe Posisi</label>
                        <div class="col-xl-8 col-xxl-6">
                            <select class="form-select @error('position_type_id') is-invalid @enderror" name="position_type_id" required>
                                <option value="">-- Pilih --</option>
                                @foreach($positionTypes as $_type)
                                    <option value="{{ $_type->id }}" @selected(old('position_type_id', $position->position_type_id) == $_type->id)>{{ $_type->name }}</option>
                                @endforeach
                            </select>
                            @error('position_type_id')
                                <small class="text-danger d-block"> {{ $message }} </small>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3 row required">
                        <label class="col-lg-4 col-xl-3 col-form-label">Kode jabatan</label>
                        <div class="col-xl-8 col-xxl-4">
                            <input type="text" class="form-control @error('kd') is-invalid @enderror" name="kd" value="{{ old('kd', $position->kd) }}" required/>
                            @error('kd')
                                <small class="text-danger d-block"> {{ $message }} </small>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3 row required">
                        <label class="col-lg-4 col-xl-3 col-form-label">Nama jabatan</label>
                        <div class="col-xl-8 col-xxl-6">
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $position->name) }}" required/>
                            @error('name')
                                <small class="text-danger d-block"> {{ $message }} </small>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label class="col-lg-4 col-xl-3 col-form-label">Deskripsi</label>
                        <div class="col-lg-8">
                            <textarea class="form-control @error('description') is-invalid @enderror" name="description">{{ old('description', $position->description) }}</textarea>
                            @error('description')
                                <small class="text-danger d-block"> {{ $message }} </small>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label class="col-lg-4 col-xl-3 col-form-label">Peran bawaan</label>
                        <div class="col-xl-8 col-xxl-6">
                            <select class="form-select @error('default_applied_role') is-invalid @enderror" name="default_applied_role">
                                <option value="">-- Pilih --</option>
                                @foreach($roles as $_role)
                                    <option value="{{ $_role->id }}" @selected(old('default_applied_role', $position->getMeta('default_applied_role')) == $_role->id)>
                                        {{ $_role->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted d-block mt-1">Peran ini diterapkan ke pengguna yang menggunakan jabatan ini</small>
                            @error('default_applied_role')
                                <small class="text-danger d-block"> {{ $message }} </small>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3 required row">
                        <label class="col-lg-4 col-xl-3 col-form-label">Visibilitas</label>
                        <div class="col-lg-8">
                            <div class="btn-group">
                                <input class="btn-check" type="radio" id="is_visible1" name="is_visible" value="1" required autocomplete="off" @checked(old('is_visible', $position->is_visible ?? 1) == 1) />
                                <label class="btn btn-outline-light text-dark" for="is_visible1"><i class="mdi mdi-eye-outline"></i></label>
                                <input class="btn-check" type="radio" id="is_visible0" name="is_visible" value="0" required autocomplete="off" @checked(old('is_visible', $position->is_visible ?? 1) === 0 || old('is_visible', $position->is_visible ?? 1) === "0") />
                                <label class="btn btn-outline-light text-dark" for="is_visible0"><i class="mdi mdi-eye-off-outline"></i></label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-7 col-xl-6">
                    <div class="mb-3 row">
                        <label class="col-lg-4 col-xl-3 col-form-label">Atasan</label>
                        <div class="col-xl-8 col-xxl-6">
                            <select class="form-select @error('parents') is-invalid @enderror" name="parents[]" style="height: 240px;" multiple>
                                @php $currentParents = old('parents', $position->parents ? $position->parents->pluck('id')->toArray() : []); @endphp
                                @foreach($positions as $department => $_positions)
                                    <optgroup label="{{ $department ?: 'Lainnya' }}">
                                        @forelse($_positions as $_pos)
                                            <option value="{{ $_pos->id }}" @selected(in_array($_pos->id, $currentParents)) @disabled($_pos->id == $position->id)>{{ $_pos->name }}</option>
                                        @empty
                                            <option value="" disabled>Tidak ada jabatan</option>
                                        @endforelse
                                    </optgroup>
                                @endforeach
                            </select>
                            <small class="text-muted d-block mt-1">Tekan dan tahan <code>ctrl</code> untuk memilih lebih dari satu</small>
                            @error('parents') <small class="text-danger d-block"> {{ $message }} </small> @enderror
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label class="col-lg-4 col-xl-3 col-form-label">Bawahan</label>
                        <div class="col-xl-8 col-xxl-6">
                            <select class="form-select @error('children') is-invalid @enderror" name="children[]" style="height: 240px;" multiple>
                                @php $currentChildren = old('children', $position->children ? $position->children->pluck('id')->toArray() : []); @endphp
                                @foreach($positions as $department => $_positions)
                                    <optgroup label="{{ $department ?: 'Lainnya' }}">
                                        @forelse($_positions as $_pos)
                                            <option value="{{ $_pos->id }}" @selected(in_array($_pos->id, $currentChildren)) @disabled($_pos->id == $position->id)>{{ $_pos->name }}</option>
                                        @empty
                                            <option value="" disabled>Tidak ada jabatan</option>
                                        @endforelse
                                    </optgroup>
                                @endforeach
                            </select>
                            @error('children') <small class="text-danger d-block"> {{ $message }} </small> @enderror
                        </div>
                    </div>

                    <div class="mb-3 required row">
                        <label class="col-lg-4 col-xl-3 col-form-label">Tingkat</label>
                        <div class="col-xl-8 col-xxl-6">
                            <div class="input-group">
                                <input type="number" class="form-control @error('level') is-invalid @enderror" name="level" value="{{ old('level', $position->level ?? 0) }}" max="10" required/>
                                <span class="input-group-text" id="level-desc"></span>
                            </div>
                            @error('level')
                                <small class="text-danger d-block"> {{ $message }} </small>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-lg-8 offset-lg-4 offset-xl-3">
                            <button class="btn btn-soft-danger"><i class="mdi mdi-check"></i> Simpan</button>
                            <a class="btn btn-ghost-light text-dark" href="{{ request('next', route('core::company.positions.index')) }}"><i class="mdi mdi-arrow-left"></i> Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const posData = {!! json_encode($positions->flatten()->pluck('name', 'level')->toArray()) !!}

    const setLevelDesc = () => {
        let levelInput = document.querySelector('[name="level"]');
        let desc = document.getElementById('level-desc');
        if(!levelInput || !desc) return;

        let val = levelInput.value;
        if (posData[val]) {
            desc.innerHTML = 'Setara ' + posData[val];
        } else {
            desc.innerHTML = 'Tidak setara dengan apapun';
        }
    }

    window.addEventListener('DOMContentLoaded', () => {
        const input = document.querySelector('[name="level"]');
        if(input) {
            ['keyup', 'change', 'input'].forEach((event) => {
                input.addEventListener(event, setLevelDesc)
            })
        }
        setLevelDesc();
    });
</script>
@endpush
