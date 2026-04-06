@foreach (collect($earnings)->sortBy(['slip_az', 'ctg_az', 'az'])->groupBy(['slip_name', 'ctg_name']) as $slip => $categories)
    <div class="card-header border-bottom-0 text-muted small text-uppercase" data-bs-toggle="collapse" data-bs-target="#collapse-income-{{ Str::slug($slip) }}" style="cursor: pointer;">
        {{ $slip }} <i class="mdi mdi-chevron-down float-end"></i>
    </div>

    <div class="list-group list-group-flush show collapse" id="collapse-income-{{ Str::slug($slip) }}">
        <table class="calc-table table align-middle mb-0">
            @foreach ($categories as $category => $items)
                <thead class="table-active">
                    <tr>
                        <th colspan="2">{{ $category }}</th>
                        <th style="width: 250px;">Total Riil (YTD)</th>
                    </tr>
                </thead>
                <tbody class="calc-income-tbody">
                    @foreach ($items as $item)
                        <tr class="calc-row">
                            <td style="width: 40px">
                                <input class="form-check-input mt-0" name="income[ctgs][{{ $loop->parent->index }}][item][{{ $loop->index }}][component_id]" type="checkbox" value="{{ $item['component_id'] }}" checked>
                            </td>
                            <td>
                                <input type="text" class="form-control form-control-sm border-0 bg-light" name="income[ctgs][{{ $loop->parent->index }}][item][{{ $loop->index }}][name]" value="{{ $item['name'] }}" readonly>
                            </td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control items-yearly text-end fw-bold"
                                           name="income[ctgs][{{ $loop->parent->index }}][item][{{ $loop->index }}][amount]"
                                           value="{{ $item['real_salary'] }}">
                                </div>
                            </td>
                        </tr>
                    @endforeach

                    {{-- Baris Subtotal Per Kategori --}}
                    <tr class="table-light">
                        <td colspan="2" class="text-end fw-bold">Subtotal {{ $category }}</td>
                        <td>
                            <input type="number" class="d-none calc-income-year-subtotal-input" value="0">
                            <input type="text" class="form-control form-control-sm text-end fw-bold bg-transparent border-0 calc-income-year-subtotal-display" readonly>
                        </td>
                    </tr>
                </tbody>
            @endforeach
        </table>
    </div>
@endforeach
