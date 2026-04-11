@extends('layouts.main')

@push('styles')
    <style>
        .form-select {
            width: 200px;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid px-4" id="banksoalkonversi-container">
        <div class="row">
            <div class="col-12 px-0">
                <div class="bg-white rounded-4 shadow-sm p-8">

                    @csrf

                    <div class="d-flex justify-content-between align-items-center mb-10">
                        <div class="d-flex gap-2">
                            <input type="text" class="form-control form-control-sm w-250px" placeholder="Cari Soal"
                                id="search-bank-soal-konversi" />
                            <select class="form-select form-select-sm" id="filter-level" data-control="select2"
                                data-hide-search="true" data-allow-clear="false">
                                @foreach ($list_level as $level)
                                    <option value="{{ $level['id'] }}">{{ $level['name'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="d-flex gap-2 align-items-center">
                            <a href="{{ route('bank-soal-konversi.order') }}"
                                class="btn btn-sm btn-info d-flex align-items-center gap-1">
                                <i class="ki-outline ki-abstract-26 fs-5 d-flex align-items-center"></i>
                                <span>Urutan Soal Level</span>
                            </a>
                            <a href="{{ route('bank-soal-konversi.form') }}"
                                class="btn btn-sm btn-primary d-flex align-items-center gap-1">
                                <i class="ki-outline ki-plus fs-5 d-flex align-items-center"></i>
                                <span>Tambah</span>
                            </a>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped" id="table-bank_soal_konversi">
                            <thead>
                                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                                    <th class="text-center">No</th>
                                    <th class="text-start">Level</th>
                                    <th class="text-start">Soal</th>
                                    <th class="text-start">Jawaban</th>
                                    <th class="text-start">Output</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <script>
        var APP_URL = window.APP_URL || "/";

        $(document).ready(function() {
            let debounceTimer;

            var table = $('#table-bank_soal_konversi').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: APP_URL + 'bank-soal-konversi/table',
                    type: 'POST',
                    data: function(d) {
                        d._token = $('meta[name="csrf-token"]').attr('content') || $('input[name=_token]').val();
                        d.level = $('#filter-level').val() || '';
                    }
                },

                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'level',
                        name: 'level'
                    },
                    {
                        data: 'soal',
                        name: 'soal'
                    },
                    {
                        data: 'jawaban',
                        name: 'jawaban'
                    },
                    {
                        data: 'output',
                        name: 'output'
                    },
                    {
                        data: 'id',
                        name: 'id',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return `
                        <button class="btn btn-sm btn-warning">✏️</button>
                        <button class="btn btn-sm btn-danger">🗑️</button>
                    `;
                        }
                    }
                ]
            });

            $('#search-bank-soal-konversi').on('keyup', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function() {
                    table.search($('#search-bank-soal-konversi').val()).draw();
                }, 300);
            });

            $('#filter-level').on('change', function() {
                table.ajax.reload();
            });
        });
    </script>
@endpush
