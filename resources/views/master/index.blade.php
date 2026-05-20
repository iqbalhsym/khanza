@extends('layout.app')

@section('title', 'Master Data')
@section('page-title', 'Master Data')
@section('breadcrumb', 'Data Dokter')

@section('content')

<div class="page-header">
  <div class="page-header-left">
    <h1>Master Data</h1>
    <p>Pengelolaan data referensi sistem</p>
  </div>
</div>

<!-- Tabs -->
<div data-tabs>
  <div class="tabs">
    <button class="tab-btn active" data-tab="dokter">Dokter</button>
    <button class="tab-btn" data-tab="poli">Poliklinik</button>
    <button class="tab-btn" data-tab="obat">Data Obat</button>
    <button class="tab-btn" data-tab="kamar">Kamar</button>
  </div>

  <!-- Dokter -->
  <div id="tab-dokter" class="tab-content active">
    <div class="filter-bar">
      <div class="input-group search-box">
        <span class="input-group-text"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></span>
        <input type="text" class="form-control" placeholder="Cari dokter…">
      </div>
      <button class="btn btn-primary btn-sm" data-modal-open="modalTambahDokter">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Tambah Dokter
      </button>
    </div>
    <div class="card">
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>Kode</th><th>Nama Dokter</th><th>Spesialisasi</th><th>Poliklinik</th><th>No. SIP</th><th>Jadwal</th><th>Status</th><th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @php $dokterList = [
              ['kode'=>'DKT-001','nama'=>'dr. Budi Santoso, SpPD','spesialis'=>'Penyakit Dalam','poli'=>'Poli Umum','sip'=>'SIP-00123/2023','jadwal'=>'Senin-Jumat','aktif'=>true],
              ['kode'=>'DKT-002','nama'=>'dr. Rina Wahyuni, SpA','spesialis'=>'Anak','poli'=>'Poli Anak','sip'=>'SIP-00124/2023','jadwal'=>'Senin, Rabu, Jumat','aktif'=>true],
              ['kode'=>'DKT-003','nama'=>'dr. Hasan Basri, SpB','spesialis'=>'Bedah','poli'=>'Poli Bedah','sip'=>'SIP-00125/2023','jadwal'=>'Selasa, Kamis','aktif'=>true],
              ['kode'=>'DKT-004','nama'=>'dr. Sari Dewi, SpOG','spesialis'=>'Kandungan','poli'=>'Poli Kandungan','sip'=>'SIP-00126/2022','jadwal'=>'Senin-Jumat','aktif'=>true],
              ['kode'=>'DKT-005','nama'=>'dr. Andi Pratama, SpM','spesialis'=>'Mata','poli'=>'Poli Mata','sip'=>'SIP-00127/2023','jadwal'=>'Rabu, Sabtu','aktif'=>false],
            ]; @endphp
            @foreach($dokterList as $d)
            <tr>
              <td class="text-muted">{{ $d['kode'] }}</td>
              <td class="fw-semibold">{{ $d['nama'] }}</td>
              <td><span class="badge badge-blue">{{ $d['spesialis'] }}</span></td>
              <td class="text-sm">{{ $d['poli'] }}</td>
              <td class="text-muted text-sm">{{ $d['sip'] }}</td>
              <td class="text-sm text-muted">{{ $d['jadwal'] }}</td>
              <td>
                @if($d['aktif'])
                  <span class="badge badge-green"><span class="badge-dot"></span>Aktif</span>
                @else
                  <span class="badge badge-gray"><span class="badge-dot"></span>Nonaktif</span>
                @endif
              </td>
              <td>
                <div class="btn-group">
                  <button class="btn btn-ghost btn-icon btn-sm" title="Edit"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>
                  <button class="btn btn-ghost btn-icon btn-sm" title="Hapus" style="color:var(--danger);"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg></button>
                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Poli -->
  <div id="tab-poli" class="tab-content">
    <div class="filter-bar">
      <div class="input-group search-box"><span class="input-group-text"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></span><input type="text" class="form-control" placeholder="Cari poliklinik…"></div>
      <button class="btn btn-primary btn-sm"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Tambah Poli</button>
    </div>
    <div class="card">
      <div class="table-wrapper">
        <table>
          <thead><tr><th>Kode</th><th>Nama Poliklinik</th><th>Gedung/Lantai</th><th>Kapasitas</th><th>Status</th><th>Aksi</th></tr></thead>
          <tbody>
            @php $poliList = [
              ['kode'=>'PLK-001','nama'=>'Poli Umum','lokasi'=>'Gedung A, Lt.1','kap'=>50,'aktif'=>true],
              ['kode'=>'PLK-002','nama'=>'Poli Penyakit Dalam','lokasi'=>'Gedung A, Lt.1','kap'=>40,'aktif'=>true],
              ['kode'=>'PLK-003','nama'=>'Poli Bedah','lokasi'=>'Gedung B, Lt.2','kap'=>30,'aktif'=>true],
              ['kode'=>'PLK-004','nama'=>'Poli Anak','lokasi'=>'Gedung C, Lt.1','kap'=>45,'aktif'=>true],
              ['kode'=>'PLK-005','nama'=>'Poli Kandungan','lokasi'=>'Gedung C, Lt.2','kap'=>35,'aktif'=>true],
              ['kode'=>'PLK-006','nama'=>'Poli Mata','lokasi'=>'Gedung A, Lt.2','kap'=>25,'aktif'=>false],
            ]; @endphp
            @foreach($poliList as $p)
            <tr>
              <td class="text-muted">{{ $p['kode'] }}</td>
              <td class="fw-semibold">{{ $p['nama'] }}</td>
              <td class="text-sm text-muted">{{ $p['lokasi'] }}</td>
              <td>{{ $p['kap'] }} pasien/hari</td>
              <td>@if($p['aktif'])<span class="badge badge-green"><span class="badge-dot"></span>Aktif</span>@else<span class="badge badge-gray">Nonaktif</span>@endif</td>
              <td><div class="btn-group"><button class="btn btn-ghost btn-icon btn-sm"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button></div></td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div id="tab-obat" class="tab-content">
    <div class="card"><div class="card-body text-center text-muted" style="padding:40px;">Data referensi obat — kelola di modul Farmasi</div></div>
  </div>

  <div id="tab-kamar" class="tab-content">
    <div class="card"><div class="card-body text-center text-muted" style="padding:40px;">Data kamar — kelola di modul Rawat Inap</div></div>
  </div>
</div>

<!-- Modal Tambah Dokter -->
<div class="modal-overlay" id="modalTambahDokter">
  <div class="modal">
    <div class="modal-header">
      <h2>Tambah Dokter Baru</h2>
      <button class="modal-close"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
    </div>
    <div class="modal-body">
      <div class="form-group"><label class="form-label">Nama Lengkap + Gelar <span class="form-required">*</span></label><input type="text" class="form-control" placeholder="dr. Nama, SpXX"></div>
      <div class="form-grid form-grid-2">
        <div class="form-group"><label class="form-label">Spesialisasi</label><input type="text" class="form-control" placeholder="Penyakit Dalam"></div>
        <div class="form-group"><label class="form-label">Poliklinik</label><select class="form-control"><option>Pilih Poli</option><option>Poli Umum</option><option>Poli Anak</option><option>Poli Bedah</option></select></div>
        <div class="form-group"><label class="form-label">No. SIP</label><input type="text" class="form-control" placeholder="SIP-XXXXX/YYYY"></div>
        <div class="form-group"><label class="form-label">No. Telepon</label><input type="tel" class="form-control" placeholder="0812xxxxxxxx"></div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-ghost modal-close">Batal</button>
      <button class="btn btn-primary">Simpan</button>
    </div>
  </div>
</div>

@endsection
