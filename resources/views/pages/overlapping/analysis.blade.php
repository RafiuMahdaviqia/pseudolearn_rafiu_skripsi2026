@extends('layouts.main')

@section('content')
    <div class="container-fluid px-4" id="analysis-container">
        <div class="row">
            <div class="col-12">
                <div class="bg-white rounded-4 shadow-sm p-8 mb-5">
                    <div class="d-flex justify-content-between align-items-center mb-10">
                        <div class="d-flex gap-2">
                            <a href="{{ route('overlapping.index') }}" class="btn btn-sm btn-secondary ps-2">
                                <i class="ki-outline ki-left fs-2"></i>
                                Kembali
                            </a>
                        </div>
                        <div class="d-flex gap-2 align-items-center">
                            <select class="form-select form-select-sm" id="filter-kelas" data-control="select2"
                                data-hide-search="true" data-allow-clear="false">
                                @foreach ($list_kelas as $kelas)
                                    <option value="{{ $kelas['id'] }}">
                                        {{ $kelas['name'] }}
                                        @if (!empty($kelas['angkatan']))
                                            ({{ $kelas['angkatan'] }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-4 px-2">
                        @csrf
                        <input type="hidden" id="soal-id" value="{{ $soal->id }}">
                        <h5>{{ $soal->judul }}</h5>
                        <div class="mt-5" id="soal">{!! $soal->soal !!}</div>
                    </div>
                    <div class="mt-5" id="analysis-result">

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/overlapping/analysis.js') }}"></script>
@endpush
