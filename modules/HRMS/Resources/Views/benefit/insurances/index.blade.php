@extends('hrms::layouts.default')

@section('title', 'Asuransi | ')

@section('navtitle', 'Asuransi')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <section>
                <div class="card border-0">
                    <div class="card-body">
                        <i class="mdi mdi-format-list-bulleted"></i> Daftar asuransi karyawan
                    </div>
                    <div class="card-body border-top border-light">
                        <form class="form-block row gy-2 gx-2" action="{{ route('hrms::benefit.insurances.registrations.index') }}" method="get">
                            <div class="flex-grow-1 col-auto">
                                <input class="form-control" name="search" placeholder="Cari nama atau nip ..." value="{{ request('search') }}" />
                            </div>
                            <div class="col-auto">
                                <a class="btn btn-light" href="{{ route('hrms::benefit.insurances.registrations.index', request()->only('trashed', 'closed')) }}"><i class="mdi mdi-refresh"></i> <span class="d-sm-none">Reset</span></a>
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-dark"><i class="mdi mdi-magnify"></i> Cari</button>
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table class="mb-0 table align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th></th>
                                    <th>Nama</th>
                                    <th>Jabatan saat ini</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employees as $employee)
                                    <tr>
                                        <td>{{ $loop->iteration + $employees->firstItem() - 1 }}</td>
                                        <td width="10">
                                            <div class="rounded-circle" style="background: url('{{ $employee->user->profile_avatar_path }}') center center no-repeat; background-size: cover; width: 32px; height: 32px;"></div>
                                        </td>
                                        <td nowrap>
                                            <strong>{{ $employee->user->name }}</strong>
                                        </td>
                                        <td><span class="badge bg-dark fw-normal">{{ $employee->position->position->name }}</span></td>
                                        <td class="py-2 text-end" nowrap>
                                            <span data-bs-toggle="collapse" data-bs-target="#collapse-{{ $employee->id }}">
                                                <button class="btn btn-soft-info rounded px-2 py-1" data-bs-toggle="tooltip" title="Lihat daftar"><i class="mdi mdi-file-tree-outline"></i></button>
                                            </span>
                                            @can('store', Modules\HRMS\Models\EmployeeInsurance::class)
                                                <a class="btn @empty($employee->salaryTemplate) disabled btn-secondary @else  btn-soft-primary @endempty rounded px-2 py-1" href="{{ route('hrms::benefit.insurances.registrations.create', ['employee' => $employee->id, 'next' => url()->full()]) }}" data-bs-toggle="tooltip" title="Tambah baru"><i class="mdi mdi-plus-circle-outline"></i></a>
                                                <div class="dropstart d-inline">
                                                    <button class="btn btn-soft-secondary text-dark rounded px-2 py-1" type="button" data-bs-toggle="dropdown"><i class="mdi mdi-dots-vertical"></i></button>
                                                    <ul class="dropdown-menu border-0 shadow">
                                                        <li>
                                                            <form class="dropdown-item form-block form-confirm" action="{{ route('hrms::benefit.insurances.registrations.reset-health-insurance', ['employee' => $employee->id, 'next' => url()->full()]) }}" method="post">
                                                                @csrf
                                                                @method('delete')

                                                                <button class="btn btn-link text-dark d-flex align-items-center p-0 text-start" title="Hapus bpjs kesehatan" data-bs-toggle="tooltip">
                                                                    <i class="mdi mdi-close-circle-outline me-2 py-1"></i>
                                                                    <span class="py-1">Hapus tarif bpjs kesehatan <br>
                                                                        <small class="text-muted">Hapus tarif asuransi bpjs kesehatan karyawan</small>
                                                                    </span>
                                                                </button>
                                                            </form>
                                                        </li>
                                                        <li class="dropdown-divider"></li>
                                                        <li>
                                                            <form class="dropdown-item form-block form-confirm" action="{{ route('hrms::benefit.insurances.registrations.reset-employee-insurance', ['employee' => $employee->id, 'next' => url()->full()]) }}" method="post">
                                                                @csrf
                                                                @method('delete')

                                                                <button class="btn btn-link text-dark d-flex align-items-center p-0 text-start" data-bs-toggle="tooltip" title="Hapus bpjs ketenagakerjaan">
                                                                    <i class="mdi mdi-close-circle-outline me-2"></i>
                                                                    <span class="py-1">Hapus tarif bpjs ketenagakerjaan <br>
                                                                        <small class="text-muted">Hapus tarif asuransi bpjs ketenagakerjaan karyawan</small>
                                                                    </span>
                                                                </button>
                                                            </form>
                                                        </li>
                                                        <li class="dropdown-divider"></li>
                                                        <li>
                                                            <form class="dropdown-item form-block form-confirm" action="{{ route('hrms::benefit.insurances.registrations.reset', ['employee' => $employee->id, 'next' => url()->full()]) }}" method="post"> @csrf @method('delete')
                                                                <button class="btn btn-link text-danger d-flex align-items-center p-0 text-start">
                                                                    <i class="mdi mdi-sync me-2"></i>
                                                                    <div>Hapus semua tarif <br> <small class="text-muted">Hapus seluruh tarif asuransi karyawan</small></div>
                                                                </button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            @endcan
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="border-0 p-0" colspan="5">
                                            <div class="collapse" id="collapse-{{ $employee->id }}">
                                                <table class="table-borderless table-hover table-sm mb-0 table align-middle">
                                                    <thead>
                                                        <tr class="text-muted small bg-light">
                                                            <th class="border-bottom fw-normal" width="5%"></th>
                                                            <th class="border-bottom fw-normal">Jenis asuransi</th>
                                                            <th class="border-bottom fw-normal">Gaji pokok</th>
                                                            <th class="border-bottom fw-normal">Tarif Perusahaan</th>
                                                            <th class="border-bottom fw-normal">Tarif karyawan</th>
                                                            <th class="border-bottom fw-normal"></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($employee->insurances as $insurance)
                                                            <tr>
                                                                <td width="5%"></td>
                                                                <td>
                                                                    {{ $insurance->price->insurance->name }}
                                                                    <div class="text-muted">{{ $insurance->price->insurance_id == 1 ? $insurance->price->conditions['group'][0] : $insurance->price['conditions']['services'][0] }}</div>
                                                                </td>
                                                                <td>
                                                                    Rp {{ \Str::money($insurance->meta['cmp_factor']) }}
                                                                </td>
                                                                <td>Rp {{ \Str::money($insurance->cmp_price) }}</td>
                                                                <td>Rp {{ \Str::money($insurance->empl_price) }}</td>
                                                                <td class="py-2 pe-2 text-end" nowrap>
                                                                    @can('destroy', $insurance)
                                                                        <form class="form-block form-confirm d-inline" action="{{ route('hrms::benefit.insurances.registrations.destroy', ['insurance' => $insurance->id, 'next' => url()->full()]) }}" method="post"> @csrf @method('delete')
                                                                            <button class="btn btn-soft-danger rounded px-2 py-1" data-bs-toggle="tooltip" title="Hapus"><i class="mdi mdi-trash-can-outline"></i></button>
                                                                        </form>
                                                                    @endcan
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="4" class="text-muted">Belum ada data asuransi karyawan,
                                                                    @if ($employee->salaryTemplate)
                                                                        @can('store', Modules\HRMS\Models\EmployeeInsurance::class)
                                                                            <a href="{{ route('hrms::benefit.insurances.registrations.create', ['employee' => $employee->id, 'next' => url()->full()]) }}">klik di sini</a> untuk menambahkan
                                                                        @endcan
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6">
                                            @include('components.notfound')
                                            @if (!request('trash'))
                                                @can('store', Modules\HRMS\Models\EmployeeInsurance::class)
                                                    <div class="mb-lg-5 mb-4 text-center">
                                                        <a class="btn btn-soft-danger" href="{{ route('hrms::benefit.insurances.registrations.create', ['year' => request('year'), 'next' => url()->full()]) }}"><i class="mdi mdi-plus"></i> Tambah distribusi cuti baru</a>
                                                    </div>
                                                @endcan
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-body">
                        {{ $employees->appends(request()->all())->links() }}
                    </div>
                </div>
            </section>
        </div>
        <div class="col-md-4">
            <div class="card card-body d-flex justify-content-between align-items-center flex-row border-0 py-4">
                <div>
                    <div class="display-4">{{ $count_employee ?? 0 }}</div>
                    <div class="small fw-bold text-secondary text-uppercase">Jumlah karyawan</div>
                </div>
                <div><i class="mdi mdi-account mdi-48px text-light"></i></div>
            </div>
            <div class="card border-0">
                <div class="card-body"><i class="mdi mdi-format-list-bulleted"></i> Menu lainnya</div>
                <div class="list-group list-group-flush border-top border-light">
                    <a class="list-group-item list-group-item-action text-danger" href="{{ route('hrms::benefit.insurances.registrations.index', ['trash' => !request('trash')]) }}"><i class="mdi mdi-trash-can-outline"></i> Lihat asuransi yang {{ request('trash') ? 'tidak' : '' }} dihapus</a>
                </div>
            </div>
            <div class="card border-0">
                <div class="card-body"><i class="mdi mdi-cog"></i> Opsi</div>
                <div class="card-body border-top">
                    <div class="mb-4">
                        <label for="max" class="form-label required">Minimal gaji di BPSJ Kesehatan</label>
                        <div class="fw-bold">Rp{{ Str::money(bpjs_min_salary(), '2', 'IDR') }}</div>
                        <cite class="small text-muted">Terbilang: {{ inwords(bpjs_min_salary()) }}</cite>
                    </div>
                    <div class="mb-4">
                        <label for="max" class="form-label required">Maksimal gaji di BPSJ Kesehatan</label>
                        <div class="fw-bold">Rp{{ Str::money(bpjs_max_salary(), '2', 'IDR') }}</div>
                        <cite class="small text-muted">Terbilang: {{ inwords(bpjs_max_salary()) }}</cite>
                    </div>
                    <hr class="border">
                    <div class="mb-4">
                        <label for="max" class="form-label required">Minimal gaji di BPSJ Ketenagakerjaan</label>
                        <div class="fw-bold">Rp{{ Str::money(bpjs_tk_min_salary(), '2', 'IDR') }}</div>
                        <cite class="small text-muted">Terbilang: {{ inwords(bpjs_tk_min_salary()) }}</cite>
                    </div>
                    <div class="mb-4">
                        <label for="max" class="form-label required">Maksimal gaji di BPSJ Jaminan Pensiun</label>
                        <div class="fw-bold">Rp{{ Str::money(bpjs_tk_pensiun_max_salary(), '2', 'IDR') }}</div>
                        <cite class="small text-muted">Terbilang: {{ inwords(bpjs_tk_pensiun_max_salary()) }}</cite>
                    </div>
                </div>
            </div>
            <form class="form-block form-confirm mb-3" action="{{ route('hrms::benefit.insurances.registrations.batch-reset-health-insurance', ['next' => url()->full()]) }}" method="post"> @csrf @method('delete')
                <button class="btn btn-outline-danger w-100 text-danger d-flex align-items-center bg-white py-3 text-start">
                    <i class="mdi mdi-trash-can-outline me-3"></i>
                    <div>Kosongkan Tarif Kesehatan <br> <small class="text-muted">Hapus seluruh tarif asuransi kesehatan karyawan</small></div>
                </button>
            </form>
            <form class="form-block form-confirm mb-3" action="{{ route('hrms::benefit.insurances.registrations.batch-reset-employee-insurance', ['next' => url()->full()]) }}" method="post"> @csrf @method('delete')
                <button class="btn btn-outline-danger w-100 text-danger d-flex align-items-center bg-white py-3 text-start">
                    <i class="mdi mdi-trash-can-outline me-3"></i>
                    <div>Kosongkan Tarif Ketenagakerjaan <br> <small class="text-muted">Hapus seluruh tarif asuransi ketenagakerjaan karyawan</small></div>
                </button>
            </form>
        </div>
    </div>
@endsection
