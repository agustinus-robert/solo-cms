@extends('finance::layouts.default')

@section('title', 'Ubah template gaji | ')
@section('navtitle', 'Ubah template gaji')

@section('content')
    <div class="d-flex align-items-center mb-4">
        <a class="text-decoration-none" href="{{ request('next', route('finance::payroll.templates.index')) }}"><i class="mdi mdi-arrow-left-circle-outline mdi-36px"></i></a>
        <div class="ms-4">
            <h2 class="mb-1">Ubah template gaji</h2>
            <div class="text-secondary">Silakan isi formulir di bawah untuk mengubah data template gaji karyawan</div>
        </div>
    </div>
    <div class="card mb-4 border-0">
        <div class="card-body">
            <form class="form-block" action="{{ route('finance::payroll.templates.update', ['template' => $template->id, 'employee' => $template->empl_id, 'next' => request('next')]) }}" method="POST"> @csrf @method('PUT')
                <div class="row mb-3">
                    <label class="col-lg-3 col-xl-2 col-form-label">Nama karyawan</label>
                    <div class="col-lg-8 col-xl-7 col-xxl-4">
                        <input type="hidden" class="form-control" value="{{ $template->empl_id }}" name="empl_id">
                        <input type="text" class="form-control" value="{{ $template->employee->user->name }}" disabled readonly>
                    </div>
                </div>
                <div class="row required mb-3">
                    <label class="col-lg-3 col-xl-2 col-form-label">Nama</label>
                    <div class="col-lg-9 col-xl-9 col-xxl-6">
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $template->name) }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-lg-3 col-xl-2 col-form-label">Prefix</label>
                    <div class="col-lg-9 col-xl-9 col-xxl-6">
                        <input type="text" class="form-control @error('prefix') is-invalid @enderror" name="prefix" value="{{ old('prefix', $template->prefix) }}">
                    </div>
                </div>
                <div class="row required mb-3">
                    <label class="col-lg-3 col-xl-2 col-form-label">Masa berlaku</label>
                    <div class="col-lg-9 col-xl-9 col-xxl-6">
                        <div class="input-group">
                            <input type="datetime-local" class="form-control @error('start_at') is-invalid @enderror" name="start_at" value="{{ $template->start_at->format('Y-m-d\TH:i') }}" required="">
                            <input type="datetime-local" class="form-control @error('end_at') is-invalid @enderror" name="end_at" value="{{ $template->end_at->format('Y-m-d\TH:i') }}" required="">
                        </div>
                    </div>
                </div>
                <div class="row required mb-3">
                    <label class="col-lg-3 col-xl-2 col-form-label">Komponen gaji</label>
                    <div class="col-xl-10">
                        <div class="table-responsive rounded border">
                            <table class="table-hover mb-0 table">
                                <thead>
                                    <tr>
                                        <th nowrap class="pt-2">Slip</th>
                                        <th nowrap class="pt-2">Kategori</th>
                                        <th nowrap class="pt-2">Komponen</th>
                                        <th class="pt-2">Nominal</th>
                                        <th class="pt-2">Deskripsi</th>
                                        <th width="50"></th>
                                    </tr>
                                </thead>
                                @foreach ($slips as $slip)
                                    @foreach ($slip->categories as $category)
                                        <tbody data-slip-name="{{ $slip->name }}" data-slip-ctgname="{{ $category->name }}">
                                            @php
                                                $items = $template->items->filter(fn($item) => $item->ctg_az == $loop->iteration && $item->slip_az == $loop->parent->iteration);
                                            @endphp

                                            @forelse ($items as $stg_component)
                                                <tr class="form-index has-add-button">
                                                    <td class="td-hide-on-add">
                                                        @if ($loop->parent->first && $loop->first) {{ $slip->name }} @endif
                                                    </td>
                                                    <td class="td-hide-on-add">
                                                        @if ($loop->first) {{ $category->name }} @endif
                                                    </td>
                                                    <td style="min-width: 120px;">
                                                        <input type="hidden" data-name="slip_az" value="{{ $loop->parent->parent->iteration }}">
                                                        <input type="hidden" data-name="slip_name" value="{{ $slip->name }}">
                                                        <input type="hidden" data-name="ctg_az" value="{{ $loop->parent->iteration }}">
                                                        <input type="hidden" data-name="ctg_name" value="{{ $category->name }}">
                                                        <input type="hidden" data-name="name" value="{{ $stg_component->name }}">
                                                        <select class="form-select" data-name="component_id" onchange="renderUnitComponent(event.currentTarget)">
                                                            <option value="" data-disabled="1">-- Pilih komponen --</option>
                                                            @foreach ($category->components as $component)
                                                                <option value="{{ $component->id }}"
                                                                    data-currency="{{ $component->currency }}"
                                                                    data-unit="{{ $component->unit?->label() }}"
                                                                    data-description="{{ $component->meta->description ?? null }}"
                                                                    data-disabled="{{ $component->unit?->disabledState() }}"
                                                                    @selected($stg_component->component_id == $component->id)>{{ $component->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td style="min-width: 120px;">
                                                        <div class="input-group">
                                                            {{-- PERBAIKAN: Tambah iscurrency="1" dan format ribuan di value --}}
                                                            <input type="text" oninput="validatedRupiah(this)" data-name="amount" class="form-control" iscurrency="1" required
                                                                @if (is_null($stg_component->amount)) disabled @endif
                                                                value="{{ number_format($stg_component->amount ?? 0, 0, ',', '.') }}">
                                                            <div class="input-group-text d-none"></div>
                                                        </div>
                                                    </td>
                                                    <td style="min-width: 120px;">
                                                        <input type="text" data-name="description" class="form-control" value="{{ $stg_component->description }}" @if (is_null($stg_component->amount)) disabled @endif>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-light text-danger rounded-circle btn-add @if (!$loop->first) d-none @endif px-2 py-1" onclick="addRow(event)"><i class="mdi mdi-plus"></i></button>
                                                        <button type="button" class="btn btn-secondary rounded-circle btn-remove @if ($loop->first) d-none @endif px-2 py-1" onclick="removeRow(event)"><i class="mdi mdi-minus"></i></button>
                                                    </td>
                                                </tr>
                                            @empty
                                                {{-- Handle jika kategori kosong agar tetap bisa tambah baris --}}
                                                <tr class="form-index has-add-button">
                                                    <td class="td-hide-on-add"></td>
                                                    <td class="td-hide-on-add">@if ($loop->first) {{ $category->name }} @endif</td>
                                                    <td style="min-width: 120px;">
                                                        <input type="hidden" data-name="slip_az" value="{{ $loop->parent->iteration }}">
                                                        <input type="hidden" data-name="slip_name" value="{{ $slip->name }}">
                                                        <input type="hidden" data-name="ctg_az" value="{{ $loop->iteration }}">
                                                        <input type="hidden" data-name="ctg_name" value="{{ $category->name }}">
                                                        <input type="hidden" data-name="name">
                                                        <select class="form-select" data-name="component_id" onchange="renderUnitComponent(event.currentTarget)">
                                                            <option value="" data-disabled="1">-- Pilih komponen --</option>
                                                            @foreach ($category->components as $component)
                                                                <option value="{{ $component->id }}" data-currency="{{ $component->currency }}" data-unit="{{ $component->unit?->label() }}" data-disabled="{{ $component->unit?->disabledState() }}">{{ $component->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <div class="input-group">
                                                            <input type="text" oninput="validatedRupiah(this)" data-name="amount" class="form-control" iscurrency="1" disabled required>
                                                            <div class="input-group-text d-none"></div>
                                                        </div>
                                                    </td>
                                                    <td><input type="text" data-name="description" class="form-control" disabled></td>
                                                    <td><button type="button" class="btn btn-light text-danger rounded-circle btn-add px-2 py-1" onclick="addRow(event)"><i class="mdi mdi-plus"></i></button></td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    @endforeach
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-lg-9 offset-lg-3 offset-xl-2">
                        <div class="form-check mb-3">
                            <input class="form-check-input" id="agreement" type="checkbox" required>
                            <label class="form-check-label" for="agreement">Dengan ini saya menyatakan data di atas adalah valid</label>
                        </div>
                        <button class="btn btn-soft-danger"><i class="mdi mdi-check"></i> Simpan</button>
                        <a class="btn btn-ghost-light text-dark" href="{{ request('next', route('finance::payroll.templates.index')) }}"><i class="mdi mdi-arrow-left"></i> Kembali</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function validatedRupiah(element) {
            let value = element.value.replace(/\D/g, '');
            if (element.getAttribute('iscurrency') == "1") {
                if (value === "") {
                    element.value = "";
                    return;
                }
                element.value = new Intl.NumberFormat('id-ID').format(value);
            } else {
                element.value = value;
            }
        }

        let primarySlip = @JSON($primarySlip);
        let secondarySlip = @JSON($secondarySlip);
        let defaultComponent = @JSON($defaultComponent);
        let settings = @JSON($settings);
        let cmptid = @JSON($cmptid);

        const addRow = (e) => {
            let tr = e.currentTarget.closest('tr');
            let el = tr.cloneNode(true)
            el.classList.remove('has-add-button');
            Array.from(el.querySelectorAll('.td-hide-on-add')).map((el) => el.innerHTML = '')
            tr.parentNode.insertAdjacentHTML('beforeend', `<tr class="form-index">${el.innerHTML}</tr>`);

            let lastRow = tr.parentNode.lastElementChild;
            lastRow.querySelector('.btn-remove').classList.remove('d-none');
            lastRow.querySelector('.btn-add').classList.add('d-none');
            lastRow.querySelector('[data-name="component_id"]').selectedIndex = 0;
            renderUnitComponent(lastRow.querySelector('[data-name="component_id"]'));

            renderNameAttribute();
        }

        const removeRow = (e) => {
            e.currentTarget.closest('tr').remove()
            renderNameAttribute();
        }

        const renderNameAttribute = () => {
            Array.from(document.querySelectorAll('.form-index')).map((tr, index) => {
                Array.from(tr.querySelectorAll('select,input')).map(input => {
                    if (input.dataset.name) {
                        input.name = `items[${index}][${input.dataset.name}]`;
                    }
                })
            })
        }

        const renderUnitComponent = (el) => {
            let checked = el.querySelector(':checked');
            let row = el.closest('.form-index');
            let amountInput = row.querySelector('[data-name="amount"]');
            let unitAddon = row.querySelector('.input-group-text');

            if (checked && checked.dataset.unit) {
                amountInput.setAttribute('iscurrency', checked.dataset.currency);
                unitAddon.classList.remove('d-none');
                unitAddon.innerHTML = checked.dataset.unit;
            } else {
                unitAddon.classList.add('d-none');
            }

            let name = (checked && checked.value) ? checked.text : '';
            row.querySelector('[data-name="name"]').value = name;
            row.querySelector('[data-name="description"]').value = checked ? checked.dataset.description : '';

            let disabled = (checked && checked.dataset.disabled == "1");
            amountInput.toggleAttribute('disabled', disabled);
            row.querySelector('[data-name="description"]').toggleAttribute('disabled', disabled);

            if (checked && checked.value) {
                let p = getPrimarySalary();
                settings.forEach(setting => {
                    if (checked.value == setting.meta.component) {
                        let l = eval(setting.meta.calculation);
                        amountInput.value = Math.round(parseFloat(l));
                        validatedRupiah(amountInput);
                    }
                });
            }
        }

        function getPrimarySalary() {
            let amt = 0;
            let parent = document.querySelector(`[data-slip-ctgname="${primarySlip.name}"]`);
            if(!parent) return 0;

            Array.from(parent.querySelectorAll('.form-index')).forEach(tr => {
                let select = tr.querySelector('[data-name="component_id"]');
                if (select && select.value == defaultComponent.id) {
                    let val = tr.querySelector('[data-name="amount"]').value.replace(/\D/g, '');
                    amt = parseFloat(val) || 0;
                }
            });
            return amt;
        }

        const newComponentRender = (e) => {
            let secondaryParent = document.querySelector(`[data-slip-ctgname="${secondarySlip.name}"]`);
            if(!secondaryParent) return;

            Array.from(secondaryParent.querySelectorAll('.form-index')).forEach(tr => {
                let select = tr.querySelector('[data-name="component_id"]');
                if (select && cmptid.includes(select.value)) {
                    let p = e.currentTarget.value.replace(/\D/g, '');
                    settings.forEach(setting => {
                        if (select.value == setting.meta.component) {
                            let l = eval(setting.meta.calculation);
                            let targetAmount = tr.querySelector('[data-name="amount"]');
                            targetAmount.value = Math.round(parseFloat(l));
                            validatedRupiah(targetAmount);
                        }
                    });
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-name="component_id"]').forEach(el => {
                if (el.value) {
                    let checked = el.querySelector(':checked');
                    let unitAddon = el.closest('tr').querySelector('.input-group-text');
                    if(checked && checked.dataset.unit) {
                        unitAddon.classList.remove('d-none');
                        unitAddon.innerHTML = checked.dataset.unit;
                    }
                }

                if (el.value == defaultComponent.id) {
                    el.closest('tr').querySelector('[data-name="amount"]').addEventListener('keyup', newComponentRender);
                }
            });
            renderNameAttribute();
        })
    </script>
@endpush
