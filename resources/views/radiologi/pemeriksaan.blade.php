@extends('layout.app')

@section('title', 'Proses Tindakan Radiologi')
@section('page-title', 'Radiologi – Pelaksanaan Tindakan')

@section('content')
<div class="card mb-20">
    <div class="card-header">
        <h3 class="card-title">Konfirmasi & Pelaksanaan</h3>
    </div>
    <div class="card-body">
        <div style="display:flex; gap:30px;">
            <div style="flex:1;">
                <table class="table-info">
                    <tr><th>Nama Pasien</th><td>: <span class="fw-bold">{{ $request->nm_pasien }}</span></td></tr>
                    <tr><th>No. Rawat</th><td>: {{ $request->no_rawat }}</td></tr>
                    <tr><th>No. RM</th><td>: {{ $request->no_rkm_medis }}</td></tr>
                </table>
            </div>
            <div style="flex:1;">
                <table class="table-info">
                    <tr><th>No. Order</th><td>: <span class="badge badge-blue">{{ $request->noorder }}</span></td></tr>
                    <tr><th>Rujukan Dari</th><td>: {{ $request->dokter_pengirim ?? '-' }}</td></tr>
                </table>
            </div>
        </div>
    </div>
</div>

<form action="{{ url('/radiologi/periksa/store') }}" method="POST">
    @csrf
    <input type="hidden" name="no_rawat" value="{{ $request->no_rawat }}">
    <input type="hidden" name="noorder" value="{{ $request->noorder }}">
    <input type="hidden" name="dokter_perujuk" value="{{ $request->dokter_perujuk }}">
    <input type="hidden" name="kd_pj" value="{{ $request->kd_pj }}">
    <input type="hidden" name="status_lanjut" value="{{ $request->status_lanjut }}">

    {{-- Error Alert --}}
    @if(session('error'))
    <div style="margin-bottom:16px;padding:14px 18px;background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.4);border-radius:10px;color:#b91c1c;">
      {{ session('error') }}
    </div>
    @endif
    @if($errors->any())
    <div style="margin-bottom:16px;padding:14px 18px;background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.4);border-radius:10px;color:#b91c1c;">
      <ul>@foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
    </div>
    @endif

    <div class="row" style="display:flex; gap:20px;">
        <div style="flex:2;">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Item Pemeriksaan</h3></div>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr><th>Pilih</th><th>Kode</th><th>Jenis Pemeriksaan</th></tr>
                        </thead>
                        <tbody>
                            @foreach($testTypes as $t)
                            <tr>
                                <td class="text-center" style="width:50px;">
                                    <input type="checkbox" name="kd_jenis_prw[]" value="{{ $t->kd_jenis_prw }}" checked style="width:18px; height:18px;">
                                </td>
                                <td>{{ $t->kd_jenis_prw }}</td>
                                <td class="fw-semibold text-primary">{{ $t->nm_perawatan }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div style="flex:1;">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Detail Tindakan</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Tgl. Periksa</label>
                        <input type="date" name="tgl_periksa" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Jam Periksa</label>
                        <input type="time" name="jam_periksa" class="form-control" value="{{ date('H:i') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Dokter Pemeriksa (PJI)</label>
                        <select name="kd_dokter" class="form-control" required>
                            <option value="">-- Pilih Dokter Radiologi --</option>
                            @foreach($dokterRadiologi as $d)
                            <option value="{{ $d->kd_dokter }}">{{ $d->nm_dokter }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Petugas (Radiografer)</label>
                        <select name="petugas" class="form-control" required>
                            <option value="">-- Pilih Petugas --</option>
                            @foreach($petugas as $p)
                            <option value="{{ $p->nip }}">{{ $p->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-footer" style="padding: 20px;">
                    <button type="submit" class="btn btn-primary btn-block">Simpan & Mulai Periksa</button>
                    <a href="{{ url('/radiologi') }}" class="btn btn-ghost btn-block" style="margin-top:10px;">Batal</a>
                </div>
            </div>
        </div>
    </div>
</form>

<style>
    .table-info th { text-align: left; padding: 5px 15px 5px 0; color: var(--text-muted); font-weight: normal; width: 120px; }
    .table-info td { padding: 5px 0; }
    .btn-block { width: 100%; justify-content: center; }
</style>
@endsection
