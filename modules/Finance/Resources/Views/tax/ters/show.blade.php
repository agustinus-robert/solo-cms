@extends('finance::layouts.default')

@section('title', 'Tambah PPh 21 | ')
@section('navtitle', 'Tambah PPh 21')

@section('content')
    <div class="d-flex align-items-center mb-4">
        <a class="text-decoration-none" href="{{ request('next', route('finance::tax.ter-taxs.index')) }}"><i class="mdi mdi-arrow-left-circle-outline mdi-36px"></i></a>
        <div class="ms-4">
            <h2 class="mb-1">Lihat PPh 21</h2>
            <div class="text-secondary">Detail PPh 21 karyawan</div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-xl-8 col-sm-12">
            <div class="card mb-4 border-0">
                <div class="card-body">
                    <i class="mdi mdi-calendar-multiselect"></i> Form PPh 21
                </div>
                <div class="table-responsive">
                    <table class="calc-table table align-middle">
                        <tr>
                            <td class="table-active" colspan="100%">
                                <strong>Penghitungan PPh 21 berdasarkan TER dan Kategori</strong>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div>Total penghasilan (Bruto)</div>
                                <div class="small text-muted"><cite>Terbilang: <span class="bruto-month-inword">{{ inwords($tax->meta?->pkp ?? 0) }}</span> rupiah.</cite></div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input type="number" name="pkp" class="form-control calc-bruto-month-subtotal-input text-end" value="{{ $tax->meta?->pkp ?? 0 }}">
                                </div>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div>PTKP Status </div>
                                <div class="small text-muted"><cite>Status PTKP berdasarkan status pernikahan dan jumlah tanggungan.</cite></div>
                            </td>
                            <td>
                                <input type="text" name="category" class="form-control calc-ptkp-category-input text-end" value="{{ $tax->meta?->category }}">
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div>Kategori </div>
                                <div class="small text-muted"><cite>Kategori TER berdasarkan status pernikahan dan jumlah tanggungan.</cite></div>
                            </td>
                            <td>
                                <input type="text" name="ter_category" class="form-control calc-ter-category-input text-end" value="{{ $tax->meta?->ter_category }}">
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div>Tarif Pajak </div>
                                <div class="small text-muted"><cite>Prosentase berdasarkan kategori dan besaran upah.</cite></div>
                                <div class="small text-muted">Terbilang: <span class="calc-ter-value-inword">{{ inwords($tax->meta?->rate ?? 0) }}</span> persen.</cite></div>
                            </td>
                            <td>
                                <input type="number" name="rate" class="form-control calc-ter-value-input text-end" value="{{ $tax->meta?->rate ?? 0 }}">
                            </td>
                            <td></td>
                        </tr>
                        @if (!empty($configs))
                            @foreach ($configs as $key => $config)
                                <tr>
                                    <td colspan="2">
                                        <div>Pengenaan pajak {{ $config['label'] }} </div>
                                        <div class="small text-muted"><cite>Prosentase ditanggung pihak {{ $config['label'] }}.</cite></div>
                                        <div class="small text-muted">Terbilang: <span class="calc-{{ $config['key'] }}-value-inword">{{ inwords($tax->meta?->{'pph_' . $config['key']} ?? 0) }}</span> rupiah.</cite></div>
                                    </td>
                                    <td>
                                        <input type="number" data-rate="{{ $config['rate'] ?? 0 }}" name="pph_{{ $config['key'] }}" class="form-control calc-{{ $config['key'] }}-value-input text-end" value="{{ $tax->meta?->{'pph_' . $config['key']} ?? 0 }}">
                                    </td>
                                    <td></td>
                                </tr>
                            @endforeach
                        @endif
                        <tr>
                            <td colspan="2">
                                <div>Total Pajak PPh21 </div>
                                <div class="small text-muted"><cite>Prosentase berdasarkan kategori dan besaran upah.</cite></div>
                                <div class="small text-muted">Terbilang: <span class="calc-ter-value-inword">{{ inwords($tax->meta?->pphtotal ?? 0) }}</span> rupiah.</cite></div>
                            </td>
                            <td>
                                <input type="number" name="pphtotal" class="form-control text-end" value="{{ $tax->meta?->pphtotal ?? 0 }}">
                            </td>
                            <td></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-12">
            <div class="card border-0">
                <div class="card-body">
                    <i class="mdi mdi-account-box-multiple-outline"></i> Detail karyawan
                </div>
                <div class="list-group list-group-flush border-top">
                    @foreach (array_filter([
            'Nama karyawan' => $tax->employee->user->name,
            'NIP' => $tax->employee->kd ?: '-',
            'Jabatan' => $tax->employee->position->position->name ?? '-',
            'Departemen' => $tax->employee->position->position->department->name ?? '-',
            'Manajer' => $tax->employee->position->position->parents->firstWhere('level.value', 4)?->employees->first()->user->name,
        ]) as $label => $value)
                        <div class="list-group-item">
                            <div class="row d-flex align-items-center">
                                <div class="col-sm-6 col-xl-12">
                                    <div class="small text-muted">{{ $label }}</div>
                                </div>
                                <div class="col-sm-6 col-xl-12 fw-bold"> {{ $value }} </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Chrome, Safari, Edge, Opera */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }
    </style>
@endpush
