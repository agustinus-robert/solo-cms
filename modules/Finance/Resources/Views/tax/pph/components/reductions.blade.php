@foreach (collect($reductions)->sortBy(['slip_az', 'ctg_az', 'az'])->groupBy(['slip_name', 'ctg_name']) as $slip => $categories)
    <div class="card-header border-bottom-0 text-muted small text-uppercase" data-bs-toggle="collapse" data-bs-target="#collapse-reduction-{{ Str::slug($slip) }}" style="cursor: pointer;">{{ $slip }} <i class="mdi mdi-chevron-down float-end"></i></div>
    <div class="list-group list-group-flush {{ $loop->first ? 'show' : '' }} collapse" id="collapse-reduction-{{ Str::slug($slip) }}">
        <input class="d-none" name="reduction[az]" value="{{ $loop->iteration }}">
        <input class="d-none" name="reduction[slip]" value="{{ $slip }}">
        <table class="calc-table table align-middle">
            @foreach (collect($categories) as $category => $items)
                <thead class="table-active">
                    <tr>
                        <th class="align-middle" colspan="3">
                            {{ $loop->iteration . '. ' . $category }} <br>
                        </th>
                        <th>
                            <input class="d-none" name="reduction[ctgs][{{ $loop->index }}][az]" value="{{ $loop->iteration }}">
                            <input class="d-none" name="reduction[ctgs][{{ $loop->index }}][ctg]" value="{{ $category }}">
                        </th>
                    </tr>
                </thead>
                <tbody class="calc-reduction-tbody">
                    @foreach ($items as $item)
                        <tr class="calc-row @if ($loop->first) calc-row-template @endif">
                            <td style="max-width: 260px">
                                <div class="input-group">
                                    <label class="input-group-text">
                                        <input class="form-check-input mt-0" name="reduction[ctgs][{{ $loop->parent->index }}][item][{{ $loop->index }}][component_id]" type="checkbox" value="{{ $item['component_id'] }}" checked>
                                    </label>
                                </div>
                            </td>
                            <td>
                                <input type="text" class="form-control" name="reduction[ctgs][{{ $loop->parent->index }}][item][{{ $loop->index }}][name]" value="{{ $item['name'] }}" required>
                            </td>
                            <td>
                                <div class="input-group flex-nowrap">
                                    <input type="number" class="form-control reduction-month-amount text-end" name="reduction[ctgs][{{ $loop->parent->index }}][item][{{ $loop->index }}][monthly]" value="{{ $x = $item['real_salary'] }}" required>
                                    <input type="number" class="form-control reduction-year-amount text-end" name="reduction[ctgs][{{ $loop->parent->index }}][item][{{ $loop->index }}][yearly]" value="{{ $y = $x * $item['real_multiplier'] }}" required>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="2">
                            <div>Subtotal potongan</div>
                            <div class="small text-muted"><cite>Terbilang: <span class="reduction-month-inword">nol</span> rupiah.</cite></div>
                            <div class="small text-muted"><cite>Terbilang: <span class="reduction-year-inword">nol</span> rupiah.</cite></div>
                        </td>
                        <td>
                            <div class="input-group">
                                <input type="number" name="reduction[ctgs][{{ $loop->parent->index }}][month]" class="form-control calc-reduction-month-subtotal-input text-end" value="">
                                <input type="number" name="reduction[ctgs][{{ $loop->parent->index }}][year]" class="form-control calc-reduction-year-subtotal-input text-end" value="">
                            </div>
                        </td>
                        <td></td>
                    </tr>
            @endforeach
            <tr>
                <td colspan="2">
                    <div>Total potongan</div>
                    <div class="small text-muted"><cite>Terbilang: <span class="totalreduction-month-inword">nol</span> rupiah.</cite></div>
                    <div class="small text-muted"><cite>Terbilang: <span class="totalreduction-year-inword">nol</span> rupiah.</cite></div>
                </td>
                <td>
                    <div class="input-group">
                        <input type="number" name="reduction[totalmonth]" class="form-control calc-totalreduction-month-subtotal-input text-end" value="">
                        <input type="number" name="reduction[totalyear]" class="form-control calc-totalreduction-year-subtotal-input text-end" value="">
                    </div>
                </td>
                <td></td>
            </tr>
            </tbody>
        </table>
    </div>
@endforeach
