@extends('finance::layouts.default')

@section('title', 'Tambah potongan | ')
@section('navtitle', 'Tambah potongan')

@section('content')
    <div class="row justify-content-center">
        <div class="col-xxl-8 col-xl-10">
            <div class="d-flex align-items-center mb-4">
                <a class="text-decoration-none" href="{{ request('next', url()->previous()) }}"><i class="mdi mdi-arrow-left-circle-outline mdi-36px"></i></a>
                <div class="ms-4">
                    <h2 class="mb-1">Tambah potongan</h2>
                    <div class="text-secondary">Silakan isi formulir di bawah ini untuk membuat potongan</div>
                </div>
            </div>
            <div class="card mb-4 border-0">
                {{-- <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <i class="mdi mdi-plus"></i> Tambah potongan
                    </div>
                </div> --}}
                <div class="card-body">
                    <form class="form-block" enctype="multipart/form-data" action="{{ route('finance::service.deduction.manage.store', ['next' => request('next', route('finance::service.deduction.manage.index'))]) }}" method="POST"> @csrf
                        <input type="hidden" name="start_at" value="{{ $start_at }}" />
                        <input type="hidden" name="end_at" value="{{ $end_at }}" />
                        <input type="hidden" name="type" value="9" />
                        <input type="hidden" name="empl_id" value="{{ $employee->id }}" />

                        <table id="fieldTable">
                            <thead>
                                <tr>
                                    <th>Keterangan</th>
                                    <th>Nominal</th>
                                    <th></th>
                                </tr>
                            </thead>

                            <tbody>
                                <input type="hidden" name="field[]" />
                                <td><input required class="form-control" type="text" name="information[]"></td>
                                <td><input required class="form-control" type="number" name="price[]" value="0"></td>
                                <td></td>
                            </tbody>
                        </table>

                        <div class="row">
                            <div class="col-2">
                                <a href="javascript:void(0)" id="addition" class="btn btn-danger mb-4">+ Tambah</a>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-lg-8 offset-lg-4 offset-xl-3">
                                <button class="btn btn-soft-danger"><i class="mdi mdi-check"></i> Simpan</button>
                                <a class="btn btn-ghost-light text-dark" href="{{ request('next', url()->previous()) }}"><i class="mdi mdi-arrow-left"></i> Kembali</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('vendor/tom-select/css/tom-select.bootstrap5.min.css') }}">
@endpush

@push('scripts')
    <script>
        // const reloadCartId = (e) => {
        //     window.location.href = e.querySelector('option:checked').dataset.route;
        // }

        document.getElementById("addition").addEventListener("click", function() {
            let table = document.getElementById("fieldTable").getElementsByTagName('tbody')[0];

            // Buat elemen row baru
            let newRow = document.createElement("tr");
            newRow.innerHTML = `
            <input type="hidden" name="field[]" />
        <td><input class="form-control" type="text" name="information[]" required></td>
        <td><input class="form-control" type="number" name="price[]" value="0" required></td>
        <td><button type="button" class="btn btn-secondary remove-field">-</button></td>
    `;

            // Tambahkan row ke dalam tabel
            table.appendChild(newRow);
        });

        // Event delegation untuk tombol hapus field
        document.getElementById("fieldTable").addEventListener("click", function(event) {
            if (event.target.classList.contains("remove-field")) {
                let row = event.target.closest("tr");
                if (row && document.querySelectorAll("#fieldTable tbody tr").length > 1) {
                    row.remove();
                }
            }
        });

        document.addEventListener("DOMContentLoaded", async () => {
            new TomSelect('[name="empl"]', {});
            new TomSelect('[name="component_id"]', {});
        });
    </script>
@endpush
