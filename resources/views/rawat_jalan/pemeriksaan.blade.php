@extends('layout.app')

@section('title', 'Pemeriksaan SOAP')
@section('page-title', 'Pemeriksaan Medis')
@section('breadcrumb', 'SOAP Rawat Jalan')

@section('content')

@if(session('error'))
<div style="margin-bottom:16px;padding:14px 18px;background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.4);border-radius:10px;color:#b91c1c;">
  {{ session('error') }}
</div>
@endif

<div class="page-header">
  <div class="page-header-left">
    <h1>Pemeriksaan Pasien (SOAP)</h1>
    <p>No. Rawat: <strong class="text-primary">{{ $reg->no_rawat }}</strong></p>
  </div>
  @php $backUrl = request('ref') === 'index' ? url('/rawat-jalan') : url('/rawat-jalan/registered/'.urlencode($reg->no_rawat)); @endphp
  <a href="{{ $backUrl }}" class="btn btn-ghost">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    Kembali
  </a>
</div>

<!-- Informasi Pasien Ringkas -->
<div class="card mb-16" style="background: var(--primary); color: #fff; border: none;">
  <div class="card-body" style="display: flex; justify-content: space-between; align-items: center; padding: 15px 25px;">
    <div>
        <div style="font-size: 18px; font-weight: 700;">{{ $reg->nm_pasien }} ({{ $reg->no_rkm_medis }})</div>
        <div style="font-size: 13px; opacity: 0.9;">{{ $reg->jk == 'L' ? 'Laki-laki' : 'Perempuan' }} • {{ $reg->nm_poli }} • {{ $reg->nm_dokter }}</div>
    </div>
    <div style="text-align: right;">
        <div style="font-size: 12px; opacity: 0.8;">No. Registrasi</div>
        <div style="font-size: 20px; font-weight: 800;">{{ $reg->no_reg }}</div>
    </div>
  </div>
</div>

<form action="{{ url('/rawat-jalan/pemeriksaan/store') }}" method="POST">
    @csrf
    <input type="hidden" name="no_rawat" value="{{ $reg->no_rawat }}">
    @if($pemeriksaan)
        <input type="hidden" name="tgl_perawatan" value="{{ $pemeriksaan->tgl_perawatan }}">
        <input type="hidden" name="jam_rawat" value="{{ $pemeriksaan->jam_rawat }}">
    @endif

    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px;">
        
        <!-- Kolom Kiri: Vitals -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <div class="card">
                <div class="card-header"><h3>Tanda Vital & Fisik</h3></div>
                <div class="card-body">
                    <div class="form-grid form-grid-2">
                        <div class="form-group">
                            <label class="form-label">Suhu (°C)</label>
                            <input type="text" class="form-control" name="suhu_tubuh" value="{{ $pemeriksaan->suhu_tubuh ?? '' }}" placeholder="36.5">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tensi (mmHg)</label>
                            <input type="text" class="form-control" name="tensi" value="{{ $pemeriksaan->tensi ?? '' }}" placeholder="120/80">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nadi (/mnt)</label>
                            <input type="text" class="form-control" name="nadi" value="{{ $pemeriksaan->nadi ?? '' }}" placeholder="80">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Respirasi (/mnt)</label>
                            <input type="text" class="form-control" name="respirasi" value="{{ $pemeriksaan->respirasi ?? '' }}" placeholder="20">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tinggi (cm)</label>
                            <input type="text" class="form-control" name="tinggi" value="{{ $pemeriksaan->tinggi ?? '' }}" placeholder="170">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Berat (kg)</label>
                            <input type="text" class="form-control" name="berat" value="{{ $pemeriksaan->berat ?? '' }}" placeholder="65">
                        </div>
                        <div class="form-group">
                            <label class="form-label">SpO2 (%)</label>
                            <input type="text" class="form-control" name="spo2" value="{{ $pemeriksaan->spo2 ?? '' }}" placeholder="98">
                        </div>
                        <div class="form-group">
                            <label class="form-label">GCS (E,V,M)</label>
                            <input type="text" class="form-control" name="gcs" value="{{ $pemeriksaan->gcs ?? '' }}" placeholder="15">
                        </div>
                    </div>
                    <div class="form-group mt-12">
                        <label class="form-label">Kesadaran</label>
                        <select class="form-control" name="kesadaran">
                            @foreach(['Compos Mentis','Somnolence','Sopor','Coma','Alert','Confusion','Voice','Pain','Unresponsive','Apatis','Delirium','Meninggal'] as $k)
                                <option value="{{ $k }}" {{ ($pemeriksaan->kesadaran ?? '') == $k ? 'selected' : '' }}>{{ $k }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3>Alergi</h3></div>
                <div class="card-body">
                    <input type="text" class="form-control" name="alergi" value="{{ $pemeriksaan->alergi ?? '' }}" placeholder="Misal: Penisilin, Debu, -">
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: SOAP -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <div class="card">
                <div class="card-header" style="border-left: 4px solid #ef4444;"><h3>[S] Subjektif (Keluhan Utama)</h3></div>
                <div class="card-body">
                    <textarea class="form-control" name="keluhan" rows="3" required placeholder="Tuliskan keluhan yang disampaikan pasien...">{{ $pemeriksaan->keluhan ?? '' }}</textarea>
                </div>
            </div>

            <div class="card">
                <div class="card-header" style="border-left: 4px solid #3b82f6;"><h3>[O] Objektif (Pemeriksaan Fisik)</h3></div>
                <div class="card-body">
                    <textarea class="form-control" name="pemeriksaan" rows="3" required placeholder="Tuliskan hasil pemeriksaan fisik dan penunjang...">{{ $pemeriksaan->pemeriksaan ?? '' }}</textarea>
                </div>
            </div>

            <div class="card">
                <div class="card-header" style="border-left: 4px solid #f59e0b;"><h3>[A] Asesmen (Penilaian/Diagnosis)</h3></div>
                <div class="card-body">
                    <textarea class="form-control" name="penilaian" rows="3" required placeholder="Tuliskan diagnosis atau penilaian medis...">{{ $pemeriksaan->penilaian ?? '' }}</textarea>
                </div>
            </div>

            <div class="card">
                <div class="card-header" style="border-left: 4px solid #10b981;"><h3>[P] Plan (Rencana Tindak Lanjut)</h3></div>
                <div class="card-body">
                    <textarea class="form-control" name="rtl" rows="3" required placeholder="Tuliskan rencana pengobatan, edukasi, atau rujukan...">{{ $pemeriksaan->rtl ?? '' }}</textarea>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3>Instruksi & Evaluasi</h3></div>
                <div class="card-body">
                    <div class="form-group mb-12">
                        <label class="form-label">Instruksi Dokter</label>
                        <input type="text" class="form-control" name="instruksi" value="{{ $pemeriksaan->instruksi ?? '' }}" placeholder="-">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Evaluasi Hasil</label>
                        <input type="text" class="form-control" name="evaluasi" value="{{ $pemeriksaan->evaluasi ?? '' }}" placeholder="-">
                    </div>
                </div>
            </div>

            <!-- Tombol Simpan -->
            <div style="display: flex; justify-content: flex-end; margin-bottom: 40px;">
                <button type="submit" class="btn btn-primary" style="padding: 12px 30px; font-size: 16px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:8px;"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Simpan Hasil Pemeriksaan SOAP
                </button>
            </div>
        </div>
    </div>
</form>

@endsection
