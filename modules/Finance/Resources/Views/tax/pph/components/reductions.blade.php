@foreach (collect($reductions)->sortBy(['slip_az', 'ctg_az', 'az'])->groupBy(['slip_name', 'ctg_name']) as $slip => $categories)
    <div class="card-header border-bottom-0 text-muted small text-uppercase" data-bs-toggle="collapse" data-bs-target="#collapse-reduction-{{ Str::slug($slip) }}" style="cursor: pointer;">
        {{ $slip }} <i class="mdi mdi-chevron-down float-end"></i>
    </div>

    <div class="list-group list-group-flush {{ $loop->first ? 'show' : '' }} collapse" id="collapse-reduction-{{ Str::slug($slip) }}">
        <input class="d-none" name="reduction[az]" value="{{ $loop->iteration }}">
        <input class="d-none" name="reduction[slip]" value="{{ $slip }}">

        <table class="calc-table table align-middle">
            @foreach ($categories as $category => $items)
                <thead class="table-active">
                    <tr>
                        <th class="align-middle" colspan="2">
                            {{ $loop->iteration . '. ' . $category }}
                        </th>
                        <th style="width: 250px;">Potongan (Riil)</th>
                    </tr>
                </thead>

                <tbody class="calc-reduction-tbody">
                    @foreach ($items as $item)
                        <tr class="calc-row">
                            <td style="width: 40px">
                                <input class="form-check-input mt-0"
                                       name="reduction[ctgs][{{ $loop->parent->index }}][item][{{ $loop->index }}][component_id]"
                                       type="checkbox" value="{{ $item['component_id'] }}"
                                       onchange="calculateAll()" checked>
                            </td>
                            <td>
                                <input type="text" class="form-control form-control-sm border-0 bg-light text-muted"
                                       name="reduction[ctgs][{{ $loop->parent->index }}][item][{{ $loop->index }}][name]"
                                       value="{{ $item['name'] }}" readonly>
                            </td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp</span>
                                    {{-- Trigger oninput biar saat angka diketik langsung update total --}}
                                    <input type="number" class="form-control reduction-year-amount text-end fw-bold"
                                           name="reduction[ctgs][{{ $loop->parent->index }}][item][{{ $loop->index }}][yearly]"
                                           value="{{ $item['real_salary'] * $item['real_multiplier'] }}"
                                           oninput="calculateAll()">
                                </div>
                            </td>
                        </tr>
                    @endforeach

                    <tr class="table-light">
                        <td colspan="2" class="text-end fw-bold small text-muted">SUBTOTAL {{ strtoupper($category) }}</td>
                        <td>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-transparent border-0">Rp</span>
                                <input type="number" class="form-control calc-reduction-year-subtotal-input text-end fw-bold bg-transparent border-0" readonly>
                            </div>
                        </td>
                    </tr>
                </tbody>
            @endforeach

            <tfoot>
                <tr class="table-dark">
                    <td colspan="2" class="text-end fw-bold">TOTAL POTONGAN {{ strtoupper($slip) }}</td>
                    <td>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-transparent border-white-50 border-0">Rp</span>
                            <input type="number" name="reduction[totalyear]" class="form-control calc-totalreduction-year-subtotal-input text-end fw-bold bg-transparent border-0 text-white" readonly>
                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
@endforeach
