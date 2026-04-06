@extends('finance::layouts.default')

@section('title', 'Kelola PPh 21 | ')
@section('navtitle', 'Kelola PPh 21')

@section('content')
    <div class="row">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <i class="mdi mdi-format-list-bulleted text-primary"></i> <span class="fw-bold">Kelola PPh 21 Karyawan (Tahunan)</span>
                </div>

                @if(session('fail'))
                    <div class="alert alert-warning alert-dismissible fade show rounded-0 d-xl-flex align-items-center border-0 py-2" role="alert">
                        <i class="mdi mdi-alert-circle-outline mdi-24px me-3"></i>
                        <div>{{ session('fail') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="mb-0 table align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-center" width="5%">No</th>
                                <th>Karyawan</th>
                                <th class="text-center">Tahun Pajak</th>
                                <th class="text-end">Nominal PPh (Pasal 17)</th>
                                <th class="text-center">Lampiran</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($employees as $employee)
                                @php
                                    $pph = $employee->taxs->first();
                                @endphp
                                <tr @if ($employee->trashed()) class="text-muted bg-light" @endif>
                                    <td class="text-center">{{ $loop->iteration + ($employees->firstItem() - 1) }}</td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $employee->user->name }}</div>
                                        @if (isset($pph->released_at))
                                            <small class="text-success">
                                                <i class="mdi mdi-check-decagram"></i> Rilis: {{ $pph->released_at->format('d/m/Y') }}
                                            </small>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-soft-dark text-dark px-3">{{ $year }}</span>
                                    </td>
                                    <td class="text-end fw-bold text-danger" nowrap>
                                        Rp{{ isset($pph->meta->pphtotal) ? number_format($pph->meta->pphtotal, 0, ',', '.') : '-' }}
                                    </td>
                                    <td class="text-center">
                                        @if (isset($pph->file) && Storage::exists($pph->file))
                                            <a class="btn btn-soft-dark btn-sm rounded-circle p-1" href="{{ Storage::url($pph->file) }}" target="_blank">
                                                <i class="mdi mdi-file-link-outline mdi-18px"></i>
                                            </a>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td nowrap class="py-1 text-end">
                                        @if (is_null($pph))
                                            <a class="btn btn-soft-primary btn-sm rounded px-3" data-bs-toggle="tooltip" title="Hitung PPh 21 Tahunan" href="{{ route('finance::tax.income-taxs.create', ['empl_id' => $employee->id, 'start_at' => $start_at->format('Y-m-d'), 'end_at' => $end_at->format('Y-m-d'), 'next' => url()->full()]) }}">
                                                <i class="mdi mdi-calculator me-1"></i> Hitung
                                            </a>
                                        @else
                                            <form class="form-block form-confirm d-inline" action="{{ route('finance::tax.income-taxs.destroy', ['income_tax' => $pph->id, 'next' => url()->full()]) }}" method="post">
                                                @csrf @method('delete')
                                                <button class="btn btn-soft-danger btn-sm rounded px-2" data-bs-toggle="tooltip" title="Hapus Bukti Potong">
                                                    <i class="mdi mdi-trash-can-outline"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">@include('components.notfound')</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-body border-top">
                    {{ $employees->appends(request()->all())->links() }}
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body border-bottom">
                    <i class="mdi mdi-filter-outline"></i> Filter Periode
                </div>
                <div class="card-body">
                    <form class="form-block" action="{{ route('finance::tax.income-taxs.index') }}" method="get">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Pilih Tahun</label>
                            <select class="form-select border-primary" name="year" onchange="this.form.submit()">
                                @for ($i = date('Y'); $i >= date('Y') - 5; $i--)
                                    <option value="{{ $i }}" @selected($year == $i)>Tahun {{ $i }}</option>
                                @endfor
                            </select>
                            {{-- Input hidden tetap ada jika controller membutuhkannya sebagai fallback --}}
                            <input type="hidden" name="start_at" value="{{ $start_at->format('Y-m-d') }}">
                            <input type="hidden" name="end_at" value="{{ $end_at->format('Y-m-d') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Departemen</label>
                            <select class="form-select" id="select-departments" name="department">
                                <option value>Semua departemen</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}" @selected(request('department') == $department->id) data-positions="{{ $department->positions->pluck('name', 'id') }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Cari Karyawan</label>
                            <input class="form-control" name="search" placeholder="Cari nama..." value="{{ request('search') }}" />
                        </div>

                        <div class="d-grid gap-2">
                            <button class="btn btn-primary" type="submit">Terapkan Filter</button>
                            <a class="btn btn-light" href="{{ route('finance::tax.income-taxs.index') }}">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <form class="form-block form-confirm mb-4" action="{{ route('finance::tax.income-taxs.release', ['start_at' => $start_at->format('Y-m-d'), 'end_at' => $end_at->format('Y-m-d')]) }}" method="post">
                @csrf
                <button class="btn btn-outline-danger w-100 p-3 text-start bg-white border-dashed shadow-sm">
                    <div class="d-flex">
                        <i class="mdi mdi-cube-send mdi-24px me-3"></i>
                        <div>
                            <span class="d-block fw-bold text-dark">Rilis PPh 21 Tahun {{ $year }}</span>
                            <small class="text-muted text-wrap">Kirim bukti potong ke semua karyawan untuk periode tahun ini.</small>
                        </div>
                    </div>
                </button>
            </form>

            <div class="card border-0 shadow-sm">
                <div class="card-body border-bottom">
                    <i class="mdi mdi-file-document-multiple-outline"></i> Laporan
                </div>
                <div class="list-group list-group-flush">
                    <a class="list-group-item list-group-item-action py-3" onclick="summaryExportExcel()" href="javascript:;">
                        <i class="mdi mdi-file-excel-outline text-success me-2"></i> Rekapitulasi PPh 21 {{ $year }}
                    </a>
                </div>
                <div class="card-body border-top bg-light py-2">
                    <small class="text-muted">Data berdasarkan periode Jan {{ $year }} s.d Des {{ $year }}</small>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('vendor/excel/excel.min.js') }}"></script>
    @include('finance::tax.pph.components.summary-excel-script')
    <script>
        const renderPositions = () => {
            let department = document.querySelector('#select-departments option:checked');
            let option = '<option value>Semua jabatan</option>';
            let selected = '{{ request('position') }}';
            if (department && department.dataset.positions) {
                let pos = JSON.parse(department.dataset.positions);
                Object.keys(pos).forEach((id) => {
                    option += `<option value="${id}" ` + (selected == id ? 'selected="selected"' : '') + `)>${pos[id]}</option>`
                })
            }
            document.getElementById('select-positions').innerHTML = option;
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('select-departments').addEventListener('change', renderPositions);
            renderPositions();
        });
    </script>
@endpush
