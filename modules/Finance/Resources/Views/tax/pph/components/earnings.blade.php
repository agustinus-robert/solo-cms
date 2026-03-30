@foreach (collect($earnings)->sortBy(['slip_az', 'ctg_az', 'az'])->groupBy(['slip_name', 'ctg_name']) as $slip => $categories)
    <div class="card-header border-bottom-0 text-muted small text-uppercase" data-bs-toggle="collapse" data-bs-target="#collapse-income-{{ Str::slug($slip) }}" style="cursor: pointer;">
        {{ $slip }} <i class="mdi mdi-chevron-down float-end"></i>
    </div>
    
    <div class="list-group list-group-flush show collapse" id="collapse-income-{{ Str::slug($slip) }}">
        <input class="d-none" name="income[az]" value="{{ $loop->iteration }}">
        <input class="d-none" name="income[slip]" value="{{ $slip }}">
        
        <table class="calc-table table align-middle mb-0">
            <thead>
                <tr class="small text-muted text-center">
                    <th colspan="2">Komponen</th>
                    <th style="width: 220px;">Bulanan</th>
                    <th style="width: 220px;">Tahunan (x12)</th>
                </tr>
            </thead>
            @foreach (collect($categories) as $category => $items)
                <thead class="table-active">
                    <tr>
                        <th class="align-middle" colspan="2">
                            {{ $loop->iteration . '. ' . $category }}
                            <input class="d-none" name="income[ctgs][{{ $loop->index }}][az]" value="{{ $loop->iteration }}">
                            <input class="d-none" name="income[ctgs][{{ $loop->index }}][ctg]" value="{{ $category }}">
                        </th>
                        <th colspan="2"></th>
                    </tr>
                </thead>
                <tbody class="calc-income-tbody">
                    @foreach ($items as $item)
                        <tr class="calc-row">
                            <td style="width: 40px">
                                <input class="form-check-input mt-0" name="income[ctgs][{{ $loop->parent->index }}][item][{{ $loop->index }}][component_id]" type="checkbox" value="{{ $item['component_id'] }}" checked>
                            </td>
                            <td>
                                <input type="text" class="form-control form-control-sm border-0 bg-light font-weight-bold" name="income[ctgs][{{ $loop->parent->index }}][item][{{ $loop->index }}][name]" value="{{ $item['name'] }}" required>
                            </td>
                            {{-- Input Bulanan --}}
                            <td>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control items-monthly text-end" 
                                           name="income[ctgs][{{ $loop->parent->index }}][item][{{ $loop->index }}][monthly]" 
                                           value="{{ $item['real_salary'] }}" 
                                           oninput="calcRowYearly(this)" required>
                                </div>
                                <div class="text-end px-1"><small class="item-rupiah-bulanan-badge text-primary"></small></div>
                            </td>
                            {{-- Preview Tahunan --}}
                            <td>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control item-yearly-display text-end bg-light" readonly value="0">
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    
                    {{-- Baris Subtotal Per Kategori --}}
                    <tr class="table-light">
                        <td colspan="2">
                            <div class="fw-bold">Subtotal {{ $category }}</div>
                            <div class="small text-muted"><cite>Terbilang: <span class="items-monthly-inword">nol</span></cite></div>
                        </td>
                        <td>
                            <input type="number" name="income[ctgs][{{ $loop->index }}][month]" class="form-control form-control-sm calc-income-month-subtotal-input text-end fw-bold bg-light" readonly>
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm calc-income-year-subtotal-display text-end fw-bold bg-light text-secondary" readonly value="0">
                        </td>
                    </tr>
                </tbody>
            @endforeach

            @if ($loop->last)
                <tfoot class="table-dark">
                    <tr>
                        <td colspan="2">
                            <div class="fw-bold">TOTAL PENDAPATAN</div>
                            <div class="small text-lightOpacity"><cite>Terbilang: <span class="totalincome-month-inword">nol</span></cite></div>
                        </td>
                        <td>
                            <input type="number" name="income[totalmonth]" class="form-control calc-totalincome-month-subtotal-input text-end fw-bold" readonly>
                        </td>
                        <td>
                            <input type="text" class="form-control calc-totalincome-year-subtotal-display text-end fw-bold bg-dark text-warning border-0" readonly value="0">
                        </td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
@endforeach