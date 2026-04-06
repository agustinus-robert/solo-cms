<div class="card-body p-0">
    <table class="table align-middle mb-0">
        <thead>
            <tr class="table-active">
                <th width="40%">Deskripsi Penghitungan</th>
                <th width="30%" class="text-center">Bulanan (Estimasi)</th>
                <th width="30%" class="text-center">Tahunan (Riil)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <div class="fw-bold">Total Penghasilan (Neto)</div>
                    <div class="small text-muted"><cite>Terbilang: <span class="bruto-month-inword">Sembilan puluh tujuh juta sembilan ratus tiga puluh delapan ribu</span> rupiah.</cite></div>
                </td>
                <td>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" class="form-control text-end bg-light calc-bruto-monthly-display" value="8.161.500" readonly>
                    </div>
                </td>
                <td>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" name="bruto_total_input" class="form-control calc-bruto-month-subtotal-input text-end fw-bold"
                            value="97938000"
                            oninput="calculatePph()">
                    </div>
                </td>
            </tr>

            <tr class="table-warning">
                <td>
                    <div class="fw-bold">Total Pengurang (PTKP + Biaya Jabatan)</div>
                    <div class="small text-muted">Komponen pengurang pajak</div>
                </td>
                <td class="text-center text-muted">-</td>
                <td>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" class="form-control text-end bg-light calc-total-pengurang-display" value="63.000.000" readonly>
                    </div>
                </td>
            </tr>

            <tr>
                <td>
                    <div class="fw-bold">Penghasilan Kena Pajak (PKP)</div>
                    <div class="small text-muted">Status: {{ $employee->tax_status ?? 'Kosong' }}</div>
                </td>
                <td>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" class="form-control text-end bg-light calc-pkp-monthly-display" value="2.911.500" readonly>
                    </div>
                </td>
                <td>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" class="form-control calc-pkp-value-input text-end" value="34938000" readonly>
                    </div>
                </td>
            </tr>

            <tr class="table-light">
                <td colspan="3"><small><strong>Detail Lapis Pajak (Pasal 17 Progresif - Non NPWP 6%)</strong></small></td>
            </tr>

            @foreach ($categories as $cat)
            <tr>
                <td>
                    <div>Lapis {{ $loop->iteration }} ({{ $is_npwp ? $cat->getPercentage() : $cat->getPercentageNonNpwp() }}%)</div>
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm text-end bg-light calc-pph{{ $loop->iteration }}-monthly-display" value="{{ $loop->first ? '174.690' : '0' }}" readonly>
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm text-end calc-pph{{ $loop->iteration }}-value-input" value="{{ $loop->first ? '2096280' : '0' }}" readonly>
                </td>
            </tr>
            @endforeach

            <tr class="table-active">
                <td colspan="3"><strong>Ringkasan Pajak Terhutang</strong></td>
            </tr>
            <tr>
                <td>Tarif Tertinggi / PPh 21</td>
                <td>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control text-end bg-light calc-ter-amount-monthly-display fw-bold text-danger" value="174.690" readonly>
                        <span class="input-group-text">/ bln</span>
                    </div>
                </td>
                <td>
                    <div class="input-group input-group-sm">
                        <input type="number" name="amount" class="form-control text-end calc-ter-amount-input fw-bold text-danger" value="2096280" readonly>
                        <span class="input-group-text">/ thn</span>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    <input type="hidden" name="ter_category" class="calc-ter-category-input" value="Pasal 17">
    <input type="hidden" name="rate" class="calc-ter-value-input" value="6">
    <input type="hidden" name="category" class="calc-ptkp-category-input" value="{{ $employee->tax_status }}">
</div>
