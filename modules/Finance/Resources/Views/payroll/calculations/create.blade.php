@extends('finance::layouts.default')

@section('title', 'Penghitungan Gaji | ')
@section('navtitle', 'Penghitungan Gaji')

@section('content')
<div class="container-fluid pb-5">
    <div class="d-flex align-items-center mb-4">
        <a class="btn btn-outline-primary border-0 p-2 shadow-sm rounded-circle" href="{{ request('next', route('finance::payroll.calculations.index')) }}">
            <i class="mdi mdi-arrow-left mdi-24px"></i>
        </a>
        <div class="ms-3">
            <h3 class="mb-0 fw-bold text-dark">Hitung Penggajian</h3>
            <p class="text-muted mb-0">Input detail penghitungan gaji untuk <strong>{{ $employee->user->name }}</strong></p>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3 border-bottom border-light">
            <div class="d-flex align-items-center">
                <div class="bg-soft-primary p-2 rounded-3 me-3">
                    <i class="mdi mdi-file-document-edit-outline text-primary mdi-24px"></i>
                </div>
                <h5 class="card-title mb-0">Formulir Penghitungan Gaji</h5>
            </div>
        </div>

        <div class="card-body p-4">
            <form action="{{ route('finance::payroll.calculations.store', ['next' => request('next', route('finance::payroll.calculations.index'))]) }}" method="POST">
                @csrf

                <div class="bg-light rounded-3 p-4 mb-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="small text-uppercase fw-semibold text-muted d-block mb-1">Nama Karyawan</label>
                            <p class="h5 mb-0 text-dark">{{ $employee->user->name }}</p>
                            <input type="hidden" name="employee" value="{{ $employee->id }}">
                        </div>
                        <div class="col-md-6">
                            <label class="small text-uppercase fw-semibold text-muted d-block mb-1">Periode Penggajian</label>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-white text-dark border py-2 px-3 rounded-2 shadow-sm">
                                    <i class="mdi mdi-calendar-range me-1"></i>
                                    {{ $start_at->isoFormat('LL') }} &mdash; {{ $end_at->isoFormat('LL') }}
                                </span>
                            </div>
                            <input type="hidden" name="start_at" value="{{ $start_at->format('Y-m-d') }}">
                            <input type="hidden" name="end_at" value="{{ $end_at->format('Y-m-d') }}">
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <label class="col-lg-3 col-form-label fw-bold required">Pilih Template Gaji</label>
                    <div class="col-lg-9">
                        <div class="card border shadow-none @error('template_id') border-danger @enderror mb-0 overflow-hidden">
                            <div class="list-group list-group-flush overflow-auto" style="max-height: 250px;">
                                @forelse ($employee->salaryTemplates as $template)
                                    <label class="list-group-item list-group-item-action border-0 d-flex align-items-center p-3 cursor-pointer">
                                        <input class="form-check-input me-3" type="radio" name="template_id" value="{{ $template->id }}"
                                            data-route="{{ route('finance::payroll.calculations.create', [...request()->only('employee', 'start_at', 'end_at', 'next'), 'template' => $template->id]) }}"
                                            @checked(old('template_id', request('template')) == $template->id)
                                            onchange="reloadWithTemplate(event.currentTarget)" required>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0 fw-bold">{{ $template->name }}</h6>
                                            <small class="text-muted">
                                                <i class="mdi mdi-clock-outline me-1"></i>{{ $template->created_at->diffForHumans() }}
                                                <span class="mx-2">|</span>
                                                <i class="mdi mdi-layers-outline me-1"></i>{{ count($template->components) }} Komponen
                                            </small>
                                        </div>
                                    </label>
                                @empty
                                    <div class="p-4 text-center">
                                        <i class="mdi mdi-alert-circle-outline mdi-36px text-warning d-block mb-2"></i>
                                        <p class="text-muted mb-0">Karyawan ini belum memiliki template. <a href="{{ route('finance::payroll.templates.create', ['employee' => $employee->id, 'next' => url()->full()]) }}" class="text-primary fw-bold">Buat Sekarang</a></p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                        @error('template_id')
                            <div class="text-danger small mt-2"><i class="mdi mdi-alert-outline me-1"></i>{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                @if ($selectedTemplate = $employee->salaryTemplates->firstWhere('id', request('template')))
                    <div class="row mb-4">
                        <label class="col-lg-3 col-form-label fw-bold required">Nama Slip Gaji</label>
                        <div class="col-lg-9">
                            <input class="form-control form-control-lg bg-light" type="text" name="name" value="{{ $selectedTemplate->prefix == 'Bonus' ? $selectedTemplate->name : $selectedTemplate->prefix . ' ' . $end_at->translatedFormat('d F') }}">
                        </div>
                    </div>
                @endif

                @if ($employee->salaryTemplates->count())
                    <div class="row mb-4">
                        <label class="col-lg-3 col-form-label fw-bold required">Detail Komponen</label>
                        <div class="col-lg-9">
                            <div class="card border shadow-none overflow-hidden">
                                @if (request('template'))
                                    @foreach ($selectedTemplate->items->sortBy(['slip_az', 'ctg_az', 'az'])->groupBy(['slip_name', 'ctg_name']) as $slip => $categories)
                                        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-2 px-3"
                                             data-bs-toggle="collapse" data-bs-target="#collapse-{{ Str::slug($slip) }}" style="cursor: pointer;">
                                            <span class="small fw-bold text-uppercase letter-spacing-1"><i class="mdi mdi-label-outline me-2"></i>{{ $slip }}</span>
                                            <i class="mdi mdi-chevron-down"></i>
                                        </div>

                                        <div class="collapse {{ $loop->first ? 'show' : '' }}" id="collapse-{{ Str::slug($slip) }}">
                                            <div class="table-responsive">
                                                <input class="d-none" name="components[{{ $loop->index }}][az]" value="{{ $loop->iteration }}">
                                                <input class="d-none" name="components[{{ $loop->index }}][slip]" value="{{ $slip }}">

                                                <table class="calc-table table table-hover mb-0 align-middle">
                                                    @foreach ($categories as $category => $items)
                                                        <thead class="bg-soft-light border-top">
                                                            <tr>
                                                                <th class="ps-3" colspan="3"><span class="text-primary fs-6">{{ $loop->iteration }}. {{ $category }}</span></th>
                                                                <th class="text-end pe-3">
                                                                    <input class="d-none" name="components[{{ $loop->parent->index }}][ctgs][{{ $loop->index }}][az]" value="{{ $loop->iteration }}">
                                                                    <input class="d-none" name="components[{{ $loop->parent->index }}][ctgs][{{ $loop->index }}][ctg]" value="{{ $category }}">
                                                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-circle" onclick="addRow(event.currentTarget)"><i class="mdi mdi-plus"></i></button>
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="calc-tbody border-bottom-0">
                                                            @foreach ($items as $item)
                                                                <tr class="calc-row @if ($loop->first) calc-row-template @endif">
                                                                    <td style="width: 35%">
                                                                        <div class="input-group input-group-sm">
                                                                            <span class="input-group-text bg-white border-end-0">
                                                                                <input class="form-check-input mt-0" type="checkbox" data-name="enable" value="1" checked>
                                                                            </span>
                                                                            <select class="form-select border-start-0 ps-1" data-name="component_id" onchange="renderAmountValue()" required>
                                                                                <option value="" data-disabled="1" data-default-amount="0" data-multiplier="1" data-operate-symbol data-operate-icon="{{ \Modules\Core\Enums\SalaryOperateEnum::NULL->icon() }}" data-operate-color="{{ \Modules\Core\Enums\SalaryOperateEnum::NULL->color() }}" data-unit="">-- Pilih --</option>
                                                                                @foreach ($components->where('category.name', $category) as $component)
                                                                                    <option value="{{ $component->id }}" @selected($component->id == $item->component_id)
                                                                                        data-disabled="{{ empty($component->meta->editable) }}"
                                                                                        data-default-amount="{{ $component->default_amount }}"
                                                                                        data-multiplier="{{ $component->multiplier }}"
                                                                                        data-operate-icon="{{ $component->operate->icon() }}"
                                                                                        data-operate-symbol="{{ $component->operate->symbol() }}"
                                                                                        data-operate-color="{{ $component->operate->color() }}"
                                                                                        data-unit="{{ $component->unit->label() }}">
                                                                                        {{ $component->name }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" class="form-control form-control-sm" data-name="name" value="{{ $item->name }}" required>
                                                                    </td>
                                                                    <td style="width: 30%">
                                                                        <input class="form-control d-none" type="text" data-name="u" value="{{ $item->component->unit->value }}" readonly>
                                                                        <div class="input-group input-group-sm flex-nowrap">
                                                                            <div class="input-group-text bg-soft-success text-success border-end-0">
                                                                                <i class="mdi mdi-plus"></i>
                                                                            </div>
                                                                            @if ($item->component->unit->prefix())
                                                                                <div class="input-group-text bg-light border-start-0 border-end-0 small">{{ $item->component->unit->prefix() }}</div>
                                                                            @endif

                                                                            <input @if ($category !== 'Rekapitulasi') oninput="validatedRupiah(this)" @endif
                                                                                data-no-rupiah="{{ $category === 'Rekapitulasi' ? 1 : 0 }}"
                                                                                type="text" class="form-control border-start-0 border-end-0 text-end fw-bold" data-name="amount" value="0" min="0" onkeyup="renderRealAmountValue(event.currentTarget)" required>

                                                                            @if ($item->component->unit->suffix())
                                                                                <div class="input-group-text bg-light border-start-0 small">{{ $item->component->unit->suffix() }}</div>
                                                                            @endif
                                                                        </div>
                                                                        <input type="number" class="d-none" data-name="n" readonly value="1">
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <button type="button" class="btn btn-link text-danger btn-sm p-0 items-btn-remove" onclick="removeRow(event.currentTarget)"><i class="mdi mdi-close-circle-outline mdi-24px"></i></button>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                            <tr @class(['calc-row-subtotal bg-soft-light', 'd-none' => $item->component->unit->disabledState()])>
                                                                <td colspan="2" class="ps-3 py-3">
                                                                    <div class="fw-bold">Subtotal {{ $category }}</div>
                                                                    <div class="small text-muted"><cite>Terbilang: <span class="calc-row-subtotal-inwords text-capitalize">nol</span> rupiah</cite></div>
                                                                </td>
                                                                <td class="pe-3">
                                                                    <input type="text" class="form-control form-control-sm border-0 bg-transparent text-end fw-bold fs-6 calc-row-subtotal-input" value="0" readonly>
                                                                </td>
                                                                <td></td>
                                                            </tr>
                                                        </tbody>
                                                    @endforeach
                                                    <tfoot class="bg-soft-primary border-top">
                                                        <tr>
                                                            <td colspan="2" class="ps-3 py-3">
                                                                <div class="fw-bold text-primary">TOTAL {{ $slip }}</div>
                                                                <div class="small text-muted"><cite>Terbilang: <span class="calc-slip-total-inwords text-capitalize">nol</span> rupiah</cite></div>
                                                            </td>
                                                            <td class="pe-3">
                                                                <input type="text" class="form-control border-0 bg-transparent text-end fw-bold fs-5 text-primary calc-slip-total-input" disabled readonly>
                                                            </td>
                                                            <td></td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="card-body text-center py-5 text-muted">
                                        <i class="mdi mdi-cursor-default-click-outline mdi-48px d-block mb-2"></i>
                                        <h5>Silakan pilih dahulu template di atas</h5>
                                    </div>
                                @endif
                            </div>
                            @error('components')
                                <small class="text-danger"><i class="mdi mdi-alert-circle me-1"></i>{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-4">
                        <label class="col-lg-3 col-form-label fw-bold fs-5 text-success">Take Home Pay (THP)</label>
                        <div class="col-lg-9">
                            <div class="card bg-soft-success border-success border-dashed p-4 shadow-none text-center">
                                <input class="d-none" type="number" name="amount" value="0">
                                <h2 class="display-6 fw-bold text-success mb-1">Rp <span class="thp-input">0</span></h2>
                                <p class="text-muted mb-0"><i class="mdi mdi-format-quote-open me-1"></i><span class="thp-inwords text-capitalize">nol</span> rupiah<i class="mdi mdi-format-quote-close ms-1"></i></p>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <label class="col-lg-3 col-form-label fw-bold">Catatan Penyesuaian</label>
                        <div class="col-lg-9">
                            <textarea class="form-control border-light shadow-sm" name="description" id="description" rows="3" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="offset-lg-3 col-lg-9">
                            <div class="form-check bg-light p-3 rounded-3 mb-4">
                                <input class="form-check-input ms-0 me-3" id="agreement" type="checkbox" required>
                                <label class="form-check-label fw-semibold" for="agreement">
                                    Saya selaku Human Resource (HR) menyatakan data di atas adalah benar dan valid.
                                </label>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm">
                                    <i class="mdi mdi-content-save-check me-2"></i>Simpan Penghitungan
                                </button>
                                <button type="reset" class="btn btn-light btn-lg">Batalkan</button>
                            </div>
                        </div>
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Styling tambahan untuk visual yang lebih halus */
    .bg-soft-primary { background-color: rgba(var(--bs-primary-rgb), 0.1); }
    .bg-soft-success { background-color: rgba(var(--bs-success-rgb), 0.1); }
    .bg-soft-light { background-color: #f8f9fa; }
    .letter-spacing-1 { letter-spacing: 1px; }
    .cursor-pointer { cursor: pointer; }
    .border-dashed { border-style: dashed !important; border-width: 2px !important; }

    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
    input[type=number] { -moz-appearance: textfield; }

    /* Fix table cell alignment */
    .calc-table td, .calc-table th { border-bottom: 1px solid #f1f1f1; }
    .calc-table tfoot td { border-bottom: none; }
</style>
@endpush

@push('scripts')
    <script>
        // Logika JavaScript Anda tetap sama persis seperti sebelumnya
        // untuk menjaga fungsionalitas penghitungan (renderAmountValue, sumSubTotal, etc.)

        const terbilang = (angka) => {
            const bilne = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas"];
            if (angka < 12) return bilne[angka];
            else if (angka < 20) return terbilang(angka - 10) + " belas";
            else if (angka < 100) return terbilang(Math.floor(angka / 10)) + " puluh " + terbilang(angka % 10);
            else if (angka < 200) return "seratus " + terbilang(angka - 100);
            else if (angka < 1000) return terbilang(Math.floor(angka / 100)) + " ratus " + terbilang(angka % 100);
            else if (angka < 2000) return "seribu " + terbilang(angka - 1000);
            else if (angka < 1000000) return terbilang(Math.floor(angka / 1000)) + " ribu " + terbilang(angka % 1000);
            else if (angka < 1000000000) return terbilang(Math.floor(angka / 1000000)) + " juta " + terbilang(angka % 1000000);
            return "";
        }

        function validatedRupiah(element) {
            if (element.dataset.noRupiah == 1) return;
            let value = element.value.replace(/\D/g, '');
            value = new Intl.NumberFormat('id-ID').format(value);
            element.value = value;
        }

        const reloadWithTemplate = (el) => {
            window.location.href = el.dataset.route;
        }

        const addRow = (el) => {
            let template = el.closest('thead').nextElementSibling.querySelector('.calc-row-template').cloneNode(true)
            template.querySelector('[data-name="component_id"]').value = "";
            el.closest('thead').nextElementSibling.insertBefore(template, el.closest('thead').nextElementSibling.querySelector('tr:last-child'));
            renderAmountValue();
        }

        const removeRow = (el) => {
            el.closest('tr').remove();
            renderInputName();
            sumSubTotal();
        }

        const renderAmountValue = () => {
            [...document.querySelectorAll('[data-name="component_id"] option:checked')].forEach((el, i) => {
                let amount = el.closest('.calc-row').querySelector('[data-name="amount"]');
                el.closest('.calc-row').querySelector('[data-name="name"]').value = el.value ? el.text : '';

                // Styling icon dinamis
                let iconContainer = amount.closest('.input-group').querySelector('.input-group-text');
                iconContainer.className = `input-group-text bg-soft-${el.dataset.operateColor || 'secondary'} text-${el.dataset.operateColor === 'danger' ? 'danger' : (el.dataset.operateColor || 'secondary')} border-end-0`;
                iconContainer.querySelector('i').className = el.dataset.operateIcon;

                let defaultAmount = (el.dataset.defaultAmount || 0);

                switch (el.dataset.operateSymbol) {
                    case '+': amount.dataset.realAmount = defaultAmount; break;
                    case '-': amount.dataset.realAmount = defaultAmount * -1; break;
                    default: amount.dataset.realAmount = 0; break;
                }

                amount.value = ['jam'].includes(el.dataset.unit.toLowerCase())
                    ? parseFloat(defaultAmount).toFixed(2)
                    : Math.round(defaultAmount * (parseFloat(el.dataset.multiplier) || 1));

                amount.dataset.operateSymbol = el.dataset.operateSymbol;
                amount.readOnly = el.dataset.disabled;
                renderRealAmountValue(el.closest('tr').querySelector('[data-name="amount"]'));

                let amountInput = el.closest('tr').querySelector('[data-name="amount"]');
                if (amountInput.dataset.noRupiah != 1) {
                    validatedRupiah(amountInput);
                }
            })
            renderInputName();
            sumSubTotal();
        }

        const renderRealAmountValue = (el) => {
            let rawValue = el.value.toString();
            let cleanValue;
            if (el.dataset.noRupiah == "1") {
                cleanValue = parseFloat(rawValue.replace(/,/g, '.')) || 0;
            } else {
                cleanValue = parseFloat(rawValue.replace(/\./g, '')) || 0;
            }

            let symbol = el.dataset.operateSymbol;
            if (symbol === '+') {
                el.dataset.realAmount = Math.abs(cleanValue);
            } else if (symbol === '-') {
                el.dataset.realAmount = Math.abs(cleanValue) * -1;
            } else {
                el.dataset.realAmount = 0;
            }
            sumSubTotal();
        }

        const sumSubTotal = () => {
            [...document.querySelectorAll('.calc-tbody')].forEach(tbody => {
                tbody.querySelectorAll('tr .items-btn-remove').forEach((tr, i) => tr.classList.toggle('disabled', i == 0))
                let subtotal = [...tbody.querySelectorAll('[data-name="amount"]')]
                    .map(el => parseFloat(el.dataset.realAmount || 0))
                    .reduce((result, x) => result + x, 0);

                tbody.querySelector('.calc-row-subtotal-input').value = Math.abs(subtotal).toLocaleString('id-ID');
                tbody.querySelector('.calc-row-subtotal-inwords').innerHTML = terbilang(Math.abs(subtotal)).toLowerCase();
            });
            sumSlipTotal();
        }

        const sumSlipTotal = () => {
            [...document.querySelectorAll('.calc-table')].forEach(table => {
                let sliptotal = [...table.querySelectorAll('[data-name="amount"]')]
                    .map(el => parseFloat(el.dataset.realAmount || 0))
                    .reduce((result, x) => result + x, 0);

                let input = table.querySelector('.calc-slip-total-input');
                input.classList.toggle('text-danger', sliptotal < 0);
                input.value = sliptotal.toLocaleString('id-ID');
                table.querySelector('.calc-slip-total-inwords').innerHTML = terbilang(Math.abs(sliptotal)).toLowerCase();
            })
            sumTHP();
        }

        const sumTHP = () => {
            let thptotal = [...document.querySelectorAll('.calc-slip-total-input')]
                .map(el => {
                    let val = el.value.toString().replace(/\./g, '').replace(/,/g, '.');
                    return parseFloat(val || 0);
                })
                .reduce((thp, x) => thp + x, 0);

            let finalTHP = Math.round(thptotal);
            document.querySelector('[name="amount"]').value = finalTHP;
            document.querySelector('.thp-input').innerHTML = finalTHP.toLocaleString('id-ID');
            document.querySelector('.thp-inwords').innerHTML = terbilang(Math.abs(finalTHP)).toLowerCase();
        }

        const renderInputName = () => {
            [...document.querySelectorAll('.calc-table')].forEach((table, h) => {
                [...table.querySelectorAll('.calc-tbody')].forEach((tbody, i) => {
                    [...tbody.querySelectorAll('.calc-row')].forEach((tr, j) => {
                        [...tr.querySelectorAll('[data-name]')].forEach(el => {
                            let result = el.dataset.name.split('.');
                            el.name = `components[${h}][ctgs][${i}][i][${j}][${result.join('][')}]`;
                            if (el.dataset.name == 'amount') {
                                tr.querySelector('[data-name="n"]').value = (tr.querySelector('[data-name="component_id"] option:checked').dataset.operateSymbol == '-' ? -1 : 1);
                            }
                        });
                    });
                });
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            renderAmountValue()
        })
    </script>
@endpush
