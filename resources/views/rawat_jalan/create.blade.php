@extends('layout.app')

@section('title', 'Pendaftaran Rawat Jalan')
@section('page-title', 'Pendaftaran')
@section('breadcrumb', 'Rawat Jalan Baru')

@section('content')

{{-- Alert error --}}
@if(session('error'))
<div style="margin-bottom:16px;padding:14px 18px;background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.4);border-radius:10px;color:#b91c1c;display:flex;align-items:center;gap:10px;">
  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
  {{ session('error') }}
</div>
@endif

@if($errors->any())
<div style="margin-bottom:16px;padding:14px 18px;background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.4);border-radius:10px;color:#b91c1c;">
  <strong>Terdapat kesalahan:</strong>
  <ul style="margin:6px 0 0 18px;">
    @foreach($errors->all() as $error)
      <li>{{ $error }}</li>
    @endforeach
  </ul>
</div>
@endif

<div class="page-header">
  <div class="page-header-left">
    <h1>Registrasi Antrian Poliklinik</h1>
    <p>Daftarkan pasien yang sudah memiliki Nomor RM ke Poliklinik tujuan</p>
  </div>
  <a href="{{ url('/rawat-jalan') }}" class="btn btn-ghost">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    Kembali
  </a>
</div>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:18px;">
  <!-- Form Registrasi -->
  <div>
    <form action="{{ url('/rawat-jalan/store') }}" method="POST">
      @csrf

      <div class="card mb-16">
        <div class="card-header">
          <h3>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" style="display:inline;vertical-align:-3px;margin-right:6px;"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
            Data Pasien & Tujuan Poli
          </h3>
        </div>
        <div class="card-body">
          <div class="form-group">
            <label class="form-label">Pilih Pasien <span class="form-required">*</span></label>
            <select class="form-control" name="no_rkm_medis" required autofocus>
              <option value="">-- Pilih Pasien (100 Terdaftar Terakhir) --</option>
              @foreach($pasien as $p)
                <option value="{{ $p->no_rkm_medis }}">{{ $p->no_rkm_medis }} - {{ $p->nm_pasien }}</option>
              @endforeach
            </select>
          </div>
          
          <div class="form-grid form-grid-2">
            <div class="form-group">
              <label class="form-label">Poliklinik Tujuan <span class="form-required">*</span></label>
              <select class="form-control" name="kd_poli" required>
                <option value="">-- Pilih Poliklinik --</option>
                @foreach($poliklinik as $poli)
                  <option value="{{ $poli->kd_poli }}">{{ $poli->nm_poli }}</option>
                @endforeach
              </select>
            </div>
            
            <div class="form-group">
              <label class="form-label">Dokter Tujuan <span class="form-required">*</span></label>
              <select class="form-control" name="kd_dokter" required>
                <option value="">-- Pilih Dokter --</option>
                @foreach($dokter as $d)
                  <option value="{{ $d->kd_dokter }}">{{ $d->nm_dokter }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="form-group" style="margin-top: 15px;">
            <label class="form-label">Cara Bayar / Jaminan <span class="form-required">*</span></label>
            <select class="form-control" name="kd_pj" required>
                <option value="BPJ">BPJS Kesehatan (BPJ)</option>
                <option value="A09">Umum / Mandiri (A09)</option>
                @foreach($penjab as $pj)
                  @if($pj->kd_pj !== 'BPJ' && $pj->kd_pj !== 'A09')
                    <option value="{{ $pj->kd_pj }}">{{ $pj->png_jawab }} ({{ $pj->kd_pj }})</option>
                  @endif
                @endforeach
            </select>
          </div>
        </div>
      </div>

      <!-- Tombol -->
      <div class="btn-group" style="justify-content:flex-end;">
        <a href="{{ url('/rawat-jalan') }}" class="btn btn-ghost">Batal</a>
        <button type="submit" class="btn btn-primary">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
          Daftarkan Antrian
        </button>
      </div>

    </form>
  </div>

  <!-- Bantuan -->
  <div style="display:flex;flex-direction:column;gap:16px;">
    <div class="card">
      <div class="card-header"><h3>Informasi Registrasi</h3></div>
      <div class="card-body text-sm" style="padding:16px;">
        Urutan cara mendaftarkan antrian:
        <ol style="margin-top:10px; padding-left:20px; color:var(--text-muted)">
          <li>Pilih pasien dari daftar (pasien harus sudah dibuat di menu Pendaftaran Pasien Baru).</li>
          <li>Tentukan Poliklinik tujuan pasien.</li>
          <li>Pilih Dokter yang bertugas di poliklinik tersebut.</li>
          <li>Tentukan Cara Bayar atau Jaminan yang dipakai.</li>
          <li>Simpan untuk mencetak nomor antrian.</li>
        </ol>
      </div>
    </div>
  </div>
</div>

@endsection
