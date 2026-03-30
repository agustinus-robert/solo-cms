@extends('hrms::layouts.default')

@section('title', 'Tambah asuransi | ')

@section('navtitle', 'Tambah asuransi')

@php
    // cari kelas aktif
    $bpjsKes = collect($currentSalaries['bpjs-kesehatan'])->firstWhere('final_salary', '>', 0);
    // karena semua sama nilainya
    $ketJkk = collect($currentSalaries['bpjs-ketenagakerjaan'])->except('Jaminan Pensiun')->first();
    // khusus pensiun
    $pensiun = data_get($currentSalaries, 'bpjs-ketenagakerjaan.Jaminan Pensiun.final_salary', 0);
@endphp

@section('content')
    <div class="d-flex align-items-center mb-4">
        <a class="text-decoration-none" href="{{ request('next', route('hrms::benefit.insurances.registrations.index')) }}"><i class="mdi mdi-arrow-left-circle-outline mdi-36px"></i></a>
        <div class="ms-4">
            <h2 class="mb-1">Tambah asuransi</h2>
            <div class="text-secondary">Silakan isi formulir di bawah untuk menambahkan data asuransi karyawan</div>
        </div>
    </div>
    <div class="card mb-4 border-0">
        <div class="card-body">
            <form id="form-add-insurances" class="form-block" action="{{ route('hrms::benefit.insurances.registrations.store', ['employee' => $employee->id, 'next' => request('next')]) }}" method="POST"> @csrf
                <div class="row mb-3">
                    <label class="col-lg-3 col-xl-2 col-form-label">Nama karyawan</label>
                    <div class="col-lg-8 col-xl-7 col-xxl-4">
                        <input type="text" class="form-control" value="{{ $employee->user->name }}" readonly>
                    </div>
                </div>
                @if ($bpjsKes)
                    <div class="row mb-3">
                        <label class="col-lg-3 col-xl-2 col-form-label">Upah BPJS Kesehatan</label>
                        <div class="col-lg-4 col-xl-4 col-xxl-4">
                            <div class="fw-bold">Rp{{ Str::money($bpjsKes['final_salary'], 0, 'IDR') }}</div>
                            <span class="small text-muted">
                                <cite>Terbilang: {{ inwords($bpjsKes['final_salary']) }} rupiah</cite>
                            </span>
                        </div>
                    </div>
                @endif
                @if ($ketJkk)
                    <div class="row mb-3">
                        <label class="col-lg-3 col-xl-2 col-form-label">Upah BPJS Ketenagakerjaan</label>
                        <div class="col-lg-4 col-xl-4 col-xxl-4">
                            <div class="fw-bold">Rp{{ Str::money($ketJkk['final_salary'], 0, 'IDR') }}</div>
                            <span class="small text-muted">
                                <cite>Terbilang: {{ inwords($ketJkk['final_salary']) }} rupiah</cite>
                            </span>
                        </div>
                    </div>
                @endif
                @if ($pensiun)
                    <div class="row mb-3">
                        <label class="col-lg-3 col-xl-2 col-form-label">Upah BPJS Pensiun</label>
                        <div class="col-lg-4 col-xl-4 col-xxl-4">
                            <div class="fw-bold">Rp{{ Str::money($pensiun, 0, 'IDR') }}</div>
                            <span class="small text-muted">
                                <cite>Terbilang: {{ inwords($pensiun) }} rupiah</cite>
                            </span>
                        </div>
                    </div>
                @endif
                <div class="row required mb-3">
                    <label class="col-lg-3 col-xl-2 col-form-label">Pilih template</label>
                    <div class="col-lg-3">
                        <select class="form-select this-filtered-onload" onchange="renderTemplate(this)">
                            <option value="">Tanpa template</option>
                            @foreach ($bpjs_template_options as $group => $items)
                                <optgroup label="{{ $group }}"></optgroup>
                                @foreach ($items as $item)
                                    <option data-group="{{ $group }}" data-select='{{ $item['data'] }}' value="{{ $item['value'] }}">
                                        &nbsp;&nbsp;&nbsp;&nbsp;{{ $item['label'] }}
                                    </option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row required mb-3">
                    <label class="col-lg-3 col-xl-2 col-form-label">Kategori asuransi</label>
                    <div class="col-xl-10">
                        <div class="table-responsive rounded border">
                            <table class="table-hover mb-0 table">
                                <thead>
                                    <tr>
                                        <th nowrap class="pt-2">Kategori</th>
                                        <th class="pt-2">Form</th>
                                        <th class="pt-2">Iuran perusahaan</th>
                                        <th class="pt-2">Iuran karyawan</th>
                                        <th width="50"></th>
                                    </tr>
                                </thead>
                                <tbody id="categories-tbody">
                                    <tr id="categories-template">
                                        <td>
                                            <select class="form-select category-select" required onchange="renderFormMeta(this)">
                                                <option value="">-- Pilih kategori --</option>
                                                @foreach ($insurances as $insurance)
                                                    <option value="{{ $insurance->id }}" data-kd="{{ $insurance->kd }}" data-price="{{ json_encode($currentSalaries) }}" data-form="{{ $insurance->meta }}" data-key="{{ json_encode(collect($insurance->meta['conditions'])->whereIn('key', ['group', 'services'])->first() ?? []) }}">{{ $insurance->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td style="max-width: 240px; width: 240px;">
                                            <div class="d-grid form-conditions gap-2"></div>
                                            <input type="hidden" class="form-calculate-id form-index" readonly data-name="price_id">
                                        </td>
                                        <td style="max-width: 240px;">
                                            <div class="form-calculate-cmp">
                                                <div class="input-group form-calculate mb-2">
                                                    <input type="number" class="form-control form-calculate-price form-index" min="0" step="0.01" data-name="meta.cmp_price" onkeyup="calculatePrice()">
                                                    <div class="input-group-text">%</div>
                                                    <input type="number" class="form-control form-calculate-factor form-index" data-name="meta.cmp_factor" onkeyup="calculatePrice()">
                                                </div>
                                            </div>
                                            <div>
                                                <input type="number" class="form-control form-calculate-result form-calculate-result form-index" data-name="cmp_price" value="0" readonly>
                                            </div>
                                        </td>
                                        <td style="max-width: 240px;">
                                            <div class="form-calculate-empl">
                                                <div class="input-group form-calculate mb-2">
                                                    <input type="number" class="form-control form-calculate-price form-index" min="0" step="0.01" data-name="meta.empl_price" onkeyup="calculatePrice()">
                                                    <div class="input-group-text">%</div>
                                                    <input type="number" class="form-control form-calculate-factor form-index" data-name="meta.empl_factor" onkeyup="calculatePrice()">
                                                </div>
                                            </div>
                                            <div>
                                                <input type="number" class="form-control form-calculate-result form-index" data-name="empl_price" value="0" readonly>
                                            </div>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-light btn-delete text-danger d-none" onclick="removeRow(event)"><i class="mdi mdi-trash-can-outline"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="p-2">
                                <button id="categories-add" type="button" class="btn btn-light text-danger"><i class="mdi mdi-plus-circle-outline"></i> Tambah kategori baru</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-lg-9 offset-lg-3 offset-xl-2">
                        <div class="form-check mb-3">
                            <input class="form-check-input" id="agreement" type="checkbox" required>
                            <label class="form-check-label" for="agreement">Dengan ini saya menyatakan data di atas adalah valid</label>
                        </div>
                        <button id="form-add-insurances-submit" class="btn btn-soft-danger"><i class="mdi mdi-check"></i> Simpan</button>
                        <a class="btn btn-ghost-light text-dark" href="{{ request('next', route('hrms::benefit.insurances.registrations.index')) }}"><i class="mdi mdi-arrow-left"></i> Kembali</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const currentSalaries = @json($currentSalaries);
        const bpjsSalary = {{ $bpjsKes['final_salary'] ?? 0 }};

        const renderTemplate = (el) => {
            [...document.querySelectorAll('.btn-delete:not(.d-none)')].forEach((el) => el.click())
            document.querySelector('.category-select').value = '';
            document.querySelector('.form-conditions').innerHTML = '';
            [...document.querySelectorAll('#categories-tbody [type="number"]')].forEach((el) => (el.value = el.readOnly ? 0 : null));
            let element = el.querySelector(':checked');
            let select = element.dataset.select;
            if (select) {
                let template = JSON.parse(select);
                template.forEach((c, i) => {
                    if (i > 0) addRow();
                    let row = document.querySelector(`#categories-tbody tr:nth-child(${i + 1})`);
                    row.querySelector('.category-select').value = c.category;

                    // AMBIL TARIF DI SINI
                    let tariffMap = getTariffMap(c.category, c.meta);

                    // parse ke form selanjutnya
                    renderFormMeta(row.querySelector('.category-select'), tariffMap);
                    if (c.meta && Object.values(c.meta)) {
                        Object.keys(c.meta).forEach((k) => {
                            row.querySelector(`[data-name="meta.${k}"]`).value = c.meta[k];
                            renderCalculation(row.querySelector(`[data-name="meta.${k}"]`));
                        })
                    }
                })
            }
            renderNameAttribute();
            applyBpjsPolicy();
        };

        const addRow = () => {
            let tr = document.querySelector('#categories-template').innerHTML;
            let tbody = document.querySelector('#categories-tbody');
            if (tbody.children.length <= 6) {
                tbody.insertAdjacentHTML('beforeend', tr);
                Array.from(tbody.children).forEach((el, i) => {
                    if (i > 0)
                        el.querySelector('.btn-delete').classList.remove('d-none');
                    if (i == tbody.children.length - 1)
                        el.querySelector('.form-conditions').innerHTML = '';
                });
            }
            renderNameAttribute();
        }

        const removeRow = (e) => {
            e.target.parentNode.closest('tr').remove()
            renderNameAttribute();
        }

        const createSelect = (name, options, label, tariffMap = null) => {
            let select = document.createElement('select');
            select.classList.add('form-select', 'form-index');
            select.dataset.name = `meta.${name}`;
            select.dataset.meta = name;

            // ENABLE ONLY IN CUSTOM RULE
            const enableRate = ['group', 'services'];

            // Option pertama
            let def = new Option(`-- Pilih ${label} --`, '');
            select.appendChild(def);

            // Options utama
            options.forEach((option) => {
                let opt = new Option(option, option);

                // Tambahkan dataset tarif ke setiap option
                if (tariffMap && enableRate.includes(name) && tariffMap[option]) {
                    opt.dataset.realSalary = tariffMap[option].real_salary ?? 0;
                    opt.dataset.finalSalary = tariffMap[option].final_salary ?? 0;
                }

                select.appendChild(opt);
            });

            select.addEventListener('change', (e) => renderCalculation(e.target))
            return select;
        };

        const renderCalculation = (el) => {
            const prices = @json($insurances->pluck('prices')->flatten());
            let price = Array.from(prices).filter((price) => {
                return Array.from(el.parentNode.children).filter(el => el.value).every((el) => {
                    if (condition = price.conditions[el.dataset.meta]) {
                        return Array.from(condition).indexOf(el.value) >= 0;
                    }
                });
            });
            if (price.length === 1 && (price = price[0])) {
                el.parentNode.parentNode.querySelector('.form-calculate-id').value = price.id;
                Array.from(el.parentNode.children).map(el => el.classList.remove('is-invalid'));
                ['cmp', 'empl'].forEach(c => {
                    let el_wrapper = el.parentNode.parentNode.parentNode.querySelector(`.form-calculate-${c}`);
                    let el_type = el_wrapper.querySelector(`.form-calculate`);

                    let price_factor = price.price_factor ?? 0;
                    let condition = price.conditions;

                    let select_wrapper = el_wrapper.closest('tr').querySelector('select[data-meta="group"], select[data-meta="services"]');
                    let selectedPrice = select_wrapper.options[select_wrapper.selectedIndex].dataset.finalSalary ?? price_factor;
                    selectedPrice = parseFloat(selectedPrice) || 0;

                    el_type.querySelector(`.form-calculate-price`).value = price[`${c}_price_type`] == 1 ? 100 : price[`${c}_price`];
                    el_type.querySelector(`.form-calculate-factor`).value = price[`${c}_price_type`] == 1 ? price[`${c}_price`] : selectedPrice;

                    calculatePrice();
                })
            } else {
                Array.from(el.parentNode.children).map(el => el.classList.add('is-invalid'));
            }
            // Disable save button
            document.getElementById('form-add-insurances-submit').classList.toggle(
                'disabled',
                Array.from(document.getElementById('form-add-insurances').querySelectorAll('input,select')).some(el => el.classList.contains('is-invalid'))
            );
        }

        const calculatePrice = () => {
            Array.from(document.querySelectorAll('.form-calculate-result')).map(el => {
                let price = parseFloat(el.parentNode.previousElementSibling.querySelector(`.form-calculate-price`).value);
                let factor = parseFloat(el.parentNode.previousElementSibling.querySelector(`.form-calculate-factor`).value);
                el.value = Math.round((price / 100 * factor) || 0);
            })
        }

        const renderFormMeta = (el, tariffMap = null) => {
            let parent = el.querySelector(':checked')
            if (!parent) return;

            // validasi tarif sudah ada atau belum
            // Auto ambil tariff berdasarkan kategori
            if (!tariffMap) {
                // category = 1 or 2
                const cat = parseInt(parent.value);

                // langsung ambil tariff dari currentSalaries
                if (cat === 1) {
                    tariffMap = currentSalaries["bpjs-kesehatan"];
                } else if (cat === 2) {
                    tariffMap = currentSalaries["bpjs-ketenagakerjaan"];
                }
            }

            let form = parent.dataset.form;
            if (form && (form = JSON.parse(form))) {
                if (form.conditions && form.conditions.length) {
                    if (group = el.parentNode.nextElementSibling.querySelector('.form-conditions')) {
                        group.innerHTML = '';
                        Array.from(form.conditions).forEach((option) => {
                            // Buat select meta
                            let select = createSelect(option.key, option.values, option.label, tariffMap);
                            group.appendChild(select)
                        })
                    }
                }
            }
            renderNameAttribute();
        }

        const renderNameAttribute = () => {
            let tbody = document.querySelector('#categories-tbody');
            Array.from(tbody.children).forEach((tr, index) => {
                Array.from(tr.querySelectorAll('.form-index')).forEach((input) => {
                    input.name = `insurances[${index}][${input.dataset.name.split('.').join('][')}]`;
                });
            });
        }

        function applyBpjsPolicy() {
            const BPJS_KES_LIMIT = {{ bpjs_kes_limit() }}

            const select = document.querySelector(".this-filtered-onload");
            if (!select) return;

            [...select.options].forEach(opt => {
                const group = opt.dataset.group || "";

                // reset dulu
                opt.disabled = false;

                if (bpjsSalary >= BPJS_KES_LIMIT) {
                    if (group.includes("BPJS Kesehatan - Kelas 2")) {
                        opt.disabled = true;
                    }
                }

                if (bpjsSalary <= BPJS_KES_LIMIT) {
                    if (group.includes("BPJS Kesehatan - Kelas 1")) {
                        opt.disabled = true;
                    }
                }
            });
        }

        function getTariffMap(category, meta) {
            if (category == 1 && meta.group) {
                return currentSalaries["bpjs-kesehatan"];
            }

            if (category == 2 && meta.services) {
                return currentSalaries["bpjs-ketenagakerjaan"];
            }

            return null;
        }

        document.addEventListener("DOMContentLoaded", () => {
            document.getElementById('categories-add').addEventListener('click', addRow);
            applyBpjsPolicy();
        });
    </script>
@endpush
