@extends('layout.app')

@section('title', 'Input Hasil Radiologi')
@section('page-title', 'Radiologi – Input Expertise')

@section('content')
<div class="card mb-20">
    <div class="card-header">
        <h3 class="card-title">Informasi Pasien & Pemeriksaan</h3>
    </div>
    <div class="card-body">
        <div class="row" style="display:flex; gap:30px;">
            <div style="flex:1;">
                <table class="table-info">
                    <tr><th>Nama Pasien</th><td>: <span class="fw-bold">{{ $exam->nm_pasien }}</span></td></tr>
                    <tr><th>No. Rawat</th><td>: {{ $exam->no_rawat }}</td></tr>
                    <tr><th>No. RM</th><td>: {{ $exam->no_rkm_medis }}</td></tr>
                </table>
            </div>
            <div style="flex:1;">
                <table class="table-info">
                    <tr><th>Tindakan</th><td>: <span class="text-primary fw-bold">{{ $exam->nm_perawatan }}</span></td></tr>
                    <tr><th>Waktu Periksa</th><td>: {{ $exam->tgl_periksa }} {{ $exam->jam }}</td></tr>
                </table>
            </div>
        </div>
    </div>
</div>

<form action="{{ url('/radiologi/store') }}" method="POST">
    @csrf
    <input type="hidden" name="no_rawat" value="{{ $exam->no_rawat }}">
    <input type="hidden" name="tgl_periksa" value="{{ $exam->tgl_periksa }}">
    <input type="hidden" name="jam" value="{{ $exam->jam }}">

    <div class="card">
        <div class="card-header" style="justify-content: space-between; display: flex; align-items: center;">
            <h3 class="card-title">Laporan Hasil Bacaan (Expertise)</h3>
            <span class="text-muted small">Silakan isi laporan medis radiologi di bawah ini.</span>
        </div>
        <div class="card-body">
            <div class="form-group">
                <textarea name="hasil" class="form-control" rows="15" style="font-family: 'Courier New', Courier, monospace; line-height: 1.6;" placeholder="Ketik hasil expertise di sini..." required>{{ $expertise->hasil ?? '' }}</textarea>
            </div>
        </div>
        <div class="card-footer" style="display:flex; justify-content: flex-end; gap: 10px; padding: 20px;">
            <a href="{{ url('/radiologi') }}" class="btn btn-ghost">Batal</a>
            <button type="submit" class="btn btn-primary px-40">Simpan Expertise</button>
        </div>
    </div>
</form>

<style>
    .table-info th { text-align: left; padding: 5px 10px 5px 0; color: var(--text-muted); font-weight: normal; width: 120px; }
    .table-info td { padding: 5px 0; }
</style>
@endsection
