@extends('layout.app')

@section('title', 'Input Hasil Laboratorium')
@section('page-title', 'Laboratorium – Input Hasil Pemeriksaan')

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible mb-20" role="alert">
    <div class="d-flex">
        <div>
            <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
        </div>
        <div>{{ session('success') }}</div>
    </div>
    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible mb-20" role="alert">
    <div class="d-flex">
        <div>
            <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><line x1="12" y1="8" x2="12" y2="12" /><line x1="12" y1="16" x2="12.01" y2="16" /></svg>
        </div>
        <div>{{ session('error') }}</div>
    </div>
    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
</div>
@endif

<div class="card mb-20">
    <div class="card-header">
        <h3 class="card-title">Informasi Pasien</h3>
    </div>
    <div class="card-body">
        <div class="row" style="display:flex; gap:30px;">
            <div style="flex:1;">
                <table class="table-info">
                    <tr><th>Nama Pasien</th><td>: <span class="fw-bold">{{ $request->nm_pasien }}</span></td></tr>
                    <tr><th>No. Rawat</th><td>: {{ $request->no_rawat }}</td></tr>
                    <tr><th>No. RM</th><td>: {{ $request->no_rkm_medis }}</td></tr>
                </table>
            </div>
            <div style="flex:1;">
                <table class="table-info">
                    <tr><th>Jenis Kelamin</th><td>: {{ $request->jk === 'L' ? 'Laki-laki' : 'Perempuan' }}</td></tr>
                    <tr><th>Tgl. Lahir</th><td>: {{ $request->tgl_lahir }}</td></tr>
                    <tr><th>No. Order</th><td>: <span class="badge badge-blue">{{ $request->noorder }}</span></td></tr>
                </table>
            </div>
        </div>
    </div>
</div>

<form action="{{ url('/laboratorium/store') }}" method="POST">
    @csrf
    <input type="hidden" name="no_rawat" value="{{ $request->no_rawat }}">
    <input type="hidden" name="noorder" value="{{ $request->noorder }}">
    <input type="hidden" name="dokter_perujuk" value="{{ $request->dokter_perujuk }}">
    <input type="hidden" name="status_pasien" value="{{ $request->status }}">

    <div class="card mb-20">
        <div class="card-body" style="background: var(--bg); padding: 15px; border-radius: 8px;">
            <div class="row" style="display:flex; gap:15px; align-items: center; flex-wrap:wrap;">
                <div class="form-group" style="margin-bottom:0;">
                    <label>Tgl. Periksa</label>
                    <input type="date" name="tgl_periksa" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label>Jam Periksa</label>
                    <input type="time" name="jam_periksa" class="form-control" value="{{ date('H:i') }}" required>
                </div>
                <div class="form-group" style="margin-bottom:0; min-width:200px;">
                    <label>Petugas Lab (NIP)</label>
                    <select name="nip" class="form-control" required>
                        @foreach($petugas_list as $p)
                            @if($p->nip !== '-')
                            <option value="{{ $p->nip }}">{{ $p->nama }} ({{ $p->nip }})</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0; flex:1;">
                    <label>Dokter Petugas (DPJP Lab)</label>
                    <select name="kd_dokter" class="form-control">
                        @foreach($dokter_list as $d)
                            <option value="{{ $d->kd_dokter }}">{{ $d->nm_dokter }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    @foreach($testTypes as $type)
    <input type="hidden" name="kd_jenis_prw[]" value="{{ $type->kd_jenis_prw }}">
    <div class="card mb-20">
        <div class="card-header" style="background: rgba(15,76,129,0.03);">
            <h3 class="card-title text-primary">{{ $type->nm_perawatan }}</h3>
        </div>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th style="width:30%;">Pemeriksaan</th>
                        <th style="width:20%;">Satuan</th>
                        <th style="width:25%;">Nilai Rujukan</th>
                        <th style="width:25%;">Hasil</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($type->templates as $t)
                    <tr>
                        <td class="fw-semibold">{{ $t->Pemeriksaan }}</td>
                        <td>{{ $t->satuan }}</td>
                        <td class="text-muted small">
                            L: {{ $t->nilai_rujukan_ld }} <br>
                            P: {{ $t->nilai_rujukan_la }}
                        </td>
                        <td>
                            <input type="text" name="hasil[{{ $t->id_template }}]" class="form-control form-control-sm" placeholder="Isi hasil...">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endforeach

    <div class="form-actions mt-20" style="display:flex; justify-content: flex-end; gap: 10px;">
        <a href="{{ url('/laboratorium') }}" class="btn btn-ghost">Batal</a>
        <button type="submit" class="btn btn-primary px-40">Simpan Hasil Lab</button>
    </div>
</form>

<style>
    .table-info th { text-align: left; padding: 5px 10px 5px 0; color: var(--text-muted); font-weight: normal; width: 120px; }
    .table-info td { padding: 5px 0; }
</style>
@endsection
