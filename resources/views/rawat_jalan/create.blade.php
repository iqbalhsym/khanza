@extends('layout.app')

@section('title', 'Pendaftaran Rawat Jalan')
@section('page-title', 'Pendaftaran')
@section('breadcrumb', 'Rawat Jalan Baru')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<style>
/* ── Patient Search Autocomplete ── */
.patient-search-wrapper {
  position: relative;
}
.patient-search-input-row {
  display: flex;
  align-items: center;
  gap: 8px;
}
.patient-search-input-row .form-control {
  flex: 1;
}
#pasien-search-box {
  padding-right: 38px;
}
.search-spinner {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  display: none;
}
.search-spinner.active {
  display: block;
}
.patient-dropdown {
  position: absolute;
  top: calc(100% + 4px);
  left: 0; right: 0;
  background: #fff;
  border: 1px solid #d1d5db;
  border-radius: 10px;
  box-shadow: 0 8px 24px rgba(0,0,0,0.12);
  z-index: 1000;
  max-height: 320px;
  overflow-y: auto;
  display: none;
}
.patient-dropdown.open {
  display: block;
}
.patient-dropdown-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 14px;
  cursor: pointer;
  border-bottom: 1px solid #f3f4f6;
  transition: background 0.15s;
}
.patient-dropdown-item:last-child {
  border-bottom: none;
}
.patient-dropdown-item:hover,
.patient-dropdown-item.highlighted {
  background: #f0f4ff;
}
.patient-avatar {
  width: 36px; height: 36px;
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-weight: 700; font-size: 14px;
  flex-shrink: 0;
}
.patient-avatar.male   { background: #dbeafe; color: #1d4ed8; }
.patient-avatar.female { background: #fce7f3; color: #be185d; }
.patient-avatar.other  { background: #f3f4f6; color: #6b7280; }
.patient-info-name {
  font-weight: 600;
  font-size: 14px;
  color: #111;
  line-height: 1.3;
}
.patient-info-meta {
  font-size: 12px;
  color: #6b7280;
  margin-top: 2px;
}
.patient-rm-badge {
  margin-left: auto;
  font-size: 11px;
  font-weight: 600;
  background: #eff6ff;
  color: #2563eb;
  padding: 2px 8px;
  border-radius: 20px;
  white-space: nowrap;
}
.dropdown-empty, .dropdown-hint {
  padding: 16px 14px;
  text-align: center;
  color: #9ca3af;
  font-size: 13px;
}
.dropdown-hint svg { display: inline; vertical-align: -3px; margin-right: 4px; }

/* Selected patient pill */
.selected-patient-card {
  display: none;
  align-items: center;
  gap: 12px;
  padding: 10px 14px;
  background: #f0f9ff;
  border: 1.5px solid #38bdf8;
  border-radius: 10px;
  margin-top: 8px;
}
.selected-patient-card.show { display: flex; }
.selected-patient-card .patient-avatar { width: 40px; height: 40px; font-size: 16px; }
.selected-patient-detail { flex: 1; }
.selected-patient-detail strong { font-size: 15px; display: block; color: #0c4a6e; }
.selected-patient-detail span { font-size: 12px; color: #0369a1; }
.btn-clear-patient {
  background: none; border: none; cursor: pointer; color: #94a3b8; padding: 4px;
  border-radius: 6px; transition: all 0.15s;
  display: flex; align-items: center;
}
.btn-clear-patient:hover { background: #fee2e2; color: #dc2626; }
</style>
@endpush

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
    <form action="{{ url('/rawat-jalan/store') }}" method="POST" id="form-daftar">
      @csrf

      <div class="card mb-16">
        <div class="card-header">
          <h3>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" style="display:inline;vertical-align:-3px;margin-right:6px;"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
            Data Pasien & Tujuan Poli
          </h3>
        </div>
        <div class="card-body">

          {{-- ── PILIH PASIEN (Live Search) ── --}}
          <div class="form-group">
            <label class="form-label" for="pasien-search-box">
              Pilih Pasien <span class="form-required">*</span>
            </label>
            {{-- Hidden field yang dikirim ke server --}}
            <input type="hidden" name="no_rkm_medis" id="no_rkm_medis_val" required>

            <div class="patient-search-wrapper" id="patient-search-wrapper">
              {{-- Input pencarian --}}
              <div style="position:relative;">
                <input
                  type="text"
                  id="pasien-search-box"
                  class="form-control"
                  placeholder="Ketik nama, No. RM, atau NIK pasien..."
                  autocomplete="off"
                  autofocus
                />
                {{-- Spinner --}}
                <span class="search-spinner" id="search-spinner">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2" style="animation:spin 0.8s linear infinite;">
                    <circle cx="12" cy="12" r="10" stroke-dasharray="30 10"/>
                  </svg>
                </span>
              </div>

              {{-- Dropdown hasil pencarian --}}
              <div class="patient-dropdown" id="pasien-dropdown">
                <div class="dropdown-hint">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                  Ketik minimal 2 karakter untuk mencari pasien
                </div>
              </div>
            </div>

            {{-- Kartu pasien terpilih --}}
            <div class="selected-patient-card" id="selected-patient-card">
              <div class="patient-avatar" id="selected-avatar">?</div>
              <div class="selected-patient-detail">
                <strong id="selected-name">-</strong>
                <span id="selected-meta">-</span>
              </div>
              <button type="button" class="btn-clear-patient" id="btn-clear-patient" title="Ganti pasien">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
              </button>
            </div>
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
                <option value="">-- Pilih Cara Bayar --</option>
                @foreach($penjab as $pj)
                  <option value="{{ $pj->kd_pj }}">{{ trim($pj->png_jawab) }} ({{ trim($pj->kd_pj) }})</option>
                @endforeach
            </select>
          </div>
        </div>
      </div>

      <!-- Tombol -->
      <div class="btn-group" style="justify-content:flex-end;">
        <a href="{{ url('/rawat-jalan') }}" class="btn btn-ghost">Batal</a>
        <button type="submit" class="btn btn-primary" id="btn-submit">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
          Daftarkan Antrian
        </button>
      </div>

    </form>
  </div>

  <!-- Bantuan & Detail Pasien Sidebar -->
  <div style="display:flex;flex-direction:column;gap:16px;">
    <!-- Petunjuk (Disembunyikan ketika pasien terpilih) -->
    <div id="help-cards-wrapper" style="display:flex; flex-direction:column; gap:16px;">
      <div class="card">
        <div class="card-header"><h3>Informasi Registrasi</h3></div>
        <div class="card-body text-sm" style="padding:16px;">
          Urutan cara mendaftarkan antrian:
          <ol style="margin-top:10px; padding-left:20px; color:var(--text-muted)">
            <li>Cari pasien dengan mengetik nama, No. RM, atau NIK di kolom pencarian.</li>
            <li>Klik nama pasien yang muncul di hasil pencarian.</li>
            <li>Tentukan Poliklinik tujuan pasien.</li>
            <li>Pilih Dokter yang bertugas di poliklinik tersebut.</li>
            <li>Tentukan Cara Bayar atau Jaminan yang dipakai.</li>
            <li>Simpan untuk mencetak nomor antrian.</li>
          </ol>
        </div>
      </div>

      <div class="card" style="border-left: 3px solid var(--primary);">
        <div class="card-body text-sm" style="padding:14px 16px;">
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;color:var(--primary);font-weight:600;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            Tips Pencarian
          </div>
          <ul style="margin:0;padding-left:16px;color:var(--text-muted);line-height:1.8;">
            <li>Ketik <strong>nama</strong> pasien</li>
            <li>Ketik <strong>No. RM</strong> (misal: 000012)</li>
            <li>Ketik <strong>NIK KTP</strong> pasien</li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Detail Pasien Terpilih (Ditampilkan secara dinamis) -->
    <div class="card" id="patient-detail-card" style="display:none; border-top: 3px solid var(--primary);">
      <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
        <h3>Detail Pasien</h3>
        <span class="badge badge-success" style="font-size:11px;">Terpilih</span>
      </div>
      <div class="card-body" style="padding:16px; max-height:75vh; overflow-y:auto; display:flex; flex-direction:column; gap:12px;">
        
        <!-- Section 1: Identitas Utama -->
        <div style="padding-bottom:12px; border-bottom:1px solid var(--border);">
          <div style="font-weight:700; font-size:11px; text-transform:uppercase; color:var(--text-muted); margin-bottom:6px;">Data Pribadi</div>
          <div style="display:flex; flex-direction:column; gap:8px;">
            <div>
              <small style="color:var(--text-muted); display:block;">Nama Lengkap</small>
              <strong id="d-nama" style="font-size:14px; color:var(--text-dark);">-</strong>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
              <div>
                <small style="color:var(--text-muted); display:block;">No. Rekam Medis</small>
                <span id="d-no-rm" style="font-weight:600; font-size:13px; color:var(--primary);">-</span>
              </div>
              <div>
                <small style="color:var(--text-muted); display:block;">NIK / KTP</small>
                <span id="d-ktp" style="font-weight:600; font-size:13px;">-</span>
              </div>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
              <div>
                <small style="color:var(--text-muted); display:block;">Jenis Kelamin</small>
                <span id="d-jk" style="font-weight:600; font-size:13px;">-</span>
              </div>
              <div>
                <small style="color:var(--text-muted); display:block;">Golongan Darah</small>
                <span id="d-gol-darah" style="font-weight:600; font-size:13px;">-</span>
              </div>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
              <div>
                <small style="color:var(--text-muted); display:block;">Tempat, Tgl Lahir</small>
                <span id="d-ttl" style="font-weight:600; font-size:13px;">-</span>
              </div>
              <div>
                <small style="color:var(--text-muted); display:block;">Umur</small>
                <span id="d-umur" style="font-weight:600; font-size:13px;">-</span>
              </div>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
              <div>
                <small style="color:var(--text-muted); display:block;">Agama</small>
                <span id="d-agama" style="font-weight:600; font-size:13px;">-</span>
              </div>
              <div>
                <small style="color:var(--text-muted); display:block;">Pekerjaan</small>
                <span id="d-pekerjaan" style="font-weight:600; font-size:13px;">-</span>
              </div>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
              <div>
                <small style="color:var(--text-muted); display:block;">Status Nikah</small>
                <span id="d-nikah" style="font-weight:600; font-size:13px;">-</span>
              </div>
              <div>
                <small style="color:var(--text-muted); display:block;">Pendidikan</small>
                <span id="d-pnd" style="font-weight:600; font-size:13px;">-</span>
              </div>
            </div>
            <div>
              <small style="color:var(--text-muted); display:block;">Dokumen Pendukung</small>
              <div id="d-data-pendukung" style="margin-top:2px;">-</div>
            </div>
          </div>
        </div>

        <!-- Section 2: Kontak & Alamat -->
        <div style="padding-bottom:12px; border-bottom:1px solid var(--border);">
          <div style="font-weight:700; font-size:11px; text-transform:uppercase; color:var(--text-muted); margin-bottom:6px;">Kontak & Alamat</div>
          <div style="display:flex; flex-direction:column; gap:8px;">
            <div>
              <small style="color:var(--text-muted); display:block;">Alamat Lengkap</small>
              <span id="d-alamat" style="font-size:13px; line-height:1.4; white-space: normal;">-</span>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
              <div>
                <small style="color:var(--text-muted); display:block;">No. Telepon / HP</small>
                <span id="d-telp" style="font-weight:600; font-size:13px;">-</span>
              </div>
              <div>
                <small style="color:var(--text-muted); display:block;">Email</small>
                <span id="d-email" style="font-weight:600; font-size:13px; word-break:break-all;">-</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Section 3: Keluarga & Penanggung Jawab -->
        <div style="padding-bottom:12px; border-bottom:1px solid var(--border);">
          <div style="font-weight:700; font-size:11px; text-transform:uppercase; color:var(--text-muted); margin-bottom:6px;">Keluarga & Penanggung Jawab</div>
          <div style="display:flex; flex-direction:column; gap:8px;">
            <div>
              <small style="color:var(--text-muted); display:block;">Nama Ibu Kandung</small>
              <span id="d-ibu" style="font-weight:600; font-size:13px;">-</span>
            </div>
            <div>
              <small style="color:var(--text-muted); display:block;">Nama Penanggung Jawab</small>
              <span id="d-nm-keluarga" style="font-weight:600; font-size:13px;">-</span>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
              <div>
                <small style="color:var(--text-muted); display:block;">Hubungan Keluarga</small>
                <span id="d-keluarga" style="font-weight:600; font-size:13px;">-</span>
              </div>
              <div>
                <small style="color:var(--text-muted); display:block;">Pekerjaan PJ</small>
                <span id="d-pekerjaanpj" style="font-weight:600; font-size:13px;">-</span>
              </div>
            </div>
            <div>
              <small style="color:var(--text-muted); display:block;">Alamat PJ</small>
              <span id="d-alamatpj" style="font-size:13px; line-height:1.4; white-space: normal;">-</span>
            </div>
          </div>
        </div>

        <!-- Section 4: Jaminan / Asuransi -->
        <div>
          <div style="font-weight:700; font-size:11px; text-transform:uppercase; color:var(--text-muted); margin-bottom:6px;">Jaminan Kesehatan</div>
          <div style="display:flex; flex-direction:column; gap:8px;">
            <div>
              <small style="color:var(--text-muted); display:block;">Cara Bayar / Jaminan</small>
              <span id="d-jaminan" style="font-weight:600; font-size:13px; color:var(--success);">-</span>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
              <div>
                <small style="color:var(--text-muted); display:block;">No. Kartu Jaminan</small>
                <span id="d-no-peserta" style="font-weight:600; font-size:13px;">-</span>
              </div>
              <div>
                <small style="color:var(--text-muted); display:block;">Instansi / Perusahaan</small>
                <span id="d-perusahaan" style="font-weight:600; font-size:13px;">-</span>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

@push('scripts')
<style>
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>
<script>
(function() {
  const searchBox    = document.getElementById('pasien-search-box');
  const dropdown     = document.getElementById('pasien-dropdown');
  const hiddenInput  = document.getElementById('no_rkm_medis_val');
  const selectedCard = document.getElementById('selected-patient-card');
  const selectedName = document.getElementById('selected-name');
  const selectedMeta = document.getElementById('selected-meta');
  const selectedAvatar = document.getElementById('selected-avatar');
  const btnClear     = document.getElementById('btn-clear-patient');
  const spinner      = document.getElementById('search-spinner');
  const wrapper      = document.getElementById('patient-search-wrapper');
  const helpCardsWrapper = document.getElementById('help-cards-wrapper');
  const patientDetailCard = document.getElementById('patient-detail-card');

  let debounceTimer  = null;
  let highlightIndex = -1;
  let currentResults = [];

  const SEARCH_URL   = '{{ url("/rawat-jalan/search-pasien") }}';
  const CSRF_TOKEN   = '{{ csrf_token() }}';

  // ── Helpers ──────────────────────────────────────────────
  function avatarClass(jk) {
    if (jk === 'L') return 'male';
    if (jk === 'P') return 'female';
    return 'other';
  }
  function avatarLetter(name) {
    return (name || '?').charAt(0).toUpperCase();
  }
  function formatTglLahir(tgl) {
    if (!tgl) return '-';
    const d = new Date(tgl);
    if (isNaN(d)) return tgl;
    return d.toLocaleDateString('id-ID', { day:'2-digit', month:'short', year:'numeric' });
  }

  // ── Render dropdown ───────────────────────────────────────
  function renderDropdown(results) {
    currentResults = results;
    highlightIndex = -1;
    dropdown.innerHTML = '';

    if (results.length === 0) {
      dropdown.innerHTML = '<div class="dropdown-empty">Pasien tidak ditemukan. Coba kata kunci lain.</div>';
    } else {
      results.forEach((p, i) => {
        const el = document.createElement('div');
        el.className = 'patient-dropdown-item';
        el.dataset.index = i;
        const cls = avatarClass(p.jk);
        const letter = avatarLetter(p.nm_pasien);
        const jkLabel = p.jk === 'L' ? 'Laki-laki' : p.jk === 'P' ? 'Perempuan' : '-';
        el.innerHTML = `
          <div class="patient-avatar ${cls}">${letter}</div>
          <div style="flex:1;min-width:0;">
            <div class="patient-info-name">${escapeHtml(p.nm_pasien)}</div>
            <div class="patient-info-meta">${jkLabel} &bull; Tgl Lahir: ${formatTglLahir(p.tgl_lahir)}</div>
          </div>
          <span class="patient-rm-badge">RM ${escapeHtml(p.no_rkm_medis)}</span>
        `;
        el.addEventListener('mousedown', (e) => {
          e.preventDefault();
          selectPatient(p);
        });
        dropdown.appendChild(el);
      });
    }

    dropdown.classList.add('open');
  }

  function escapeHtml(text) {
    const div = document.createElement('div');
    div.appendChild(document.createTextNode(text || ''));
    return div.innerHTML;
  }

  // ── Select patient ────────────────────────────────────────
  function selectPatient(p) {
    hiddenInput.value = p.no_rkm_medis;
    hiddenInput.required = false; // already filled

    // Show card
    const cls = avatarClass(p.jk);
    const letter = avatarLetter(p.nm_pasien);
    const jkLabel = p.jk === 'L' ? 'Laki-laki' : p.jk === 'P' ? 'Perempuan' : '-';
    selectedAvatar.textContent = letter;
    selectedAvatar.className   = 'patient-avatar ' + cls;
    selectedName.textContent   = p.nm_pasien;
    selectedMeta.textContent   = `No. RM: ${p.no_rkm_medis}  •  ${jkLabel}  •  NIK: ${p.no_ktp || '-'}`;

    selectedCard.classList.add('show');

    // Populate Right Sidebar Detail Card
    if (patientDetailCard) {
      document.getElementById('d-nama').textContent = p.nm_pasien || '-';
      document.getElementById('d-no-rm').textContent = p.no_rkm_medis || '-';
      document.getElementById('d-ktp').textContent = p.no_ktp || '-';
      document.getElementById('d-jk').textContent = jkLabel;
      document.getElementById('d-gol-darah').textContent = p.gol_darah || '-';
      
      const ttlText = (p.tmp_lahir ? p.tmp_lahir + ', ' : '') + formatTglLahir(p.tgl_lahir);
      document.getElementById('d-ttl').textContent = ttlText;
      document.getElementById('d-umur').textContent = p.umur || '-';
      document.getElementById('d-agama').textContent = p.agama || '-';
      document.getElementById('d-pekerjaan').textContent = p.pekerjaan || '-';
      document.getElementById('d-nikah').textContent = p.stts_nikah || '-';
      document.getElementById('d-pnd').textContent = p.pnd || '-';

      const dDataPendukung = document.getElementById('d-data-pendukung');
      if (dDataPendukung) {
        if (p.data_pendukung) {
          dDataPendukung.innerHTML = `<a href="/uploads/data_pendukung/${p.data_pendukung}" target="_blank" style="color: #206bc4; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            Unduh Dokumen
          </a>`;
        } else {
          dDataPendukung.textContent = '-';
        }
      }

      document.getElementById('d-alamat').textContent = p.alamat || '-';
      document.getElementById('d-telp').textContent = p.no_tlp || '-';
      document.getElementById('d-email').textContent = p.email || '-';
      
      document.getElementById('d-ibu').textContent = p.nm_ibu || '-';
      document.getElementById('d-nm-keluarga').textContent = p.namakeluarga || '-';
      document.getElementById('d-keluarga').textContent = p.keluarga || '-';
      document.getElementById('d-pekerjaanpj').textContent = p.pekerjaanpj || '-';
      
      let alamatPjText = '-';
      if (p.alamatpj) {
        alamatPjText = p.alamatpj;
        const parts = [];
        if (p.kelurahanpj) parts.push(p.kelurahanpj);
        if (p.kecamatanpj) parts.push(p.kecamatanpj);
        if (p.kabupatenpj) parts.push(p.kabupatenpj);
        if (p.propinsipj) parts.push(p.propinsipj);
        if (parts.length > 0) {
          alamatPjText += ', ' + parts.join(', ');
        }
      }
      document.getElementById('d-alamatpj').textContent = alamatPjText;

      const jaminanText = p.png_jawab ? `${p.png_jawab} (${p.kd_pj})` : (p.kd_pj || '-');
      document.getElementById('d-jaminan').textContent = jaminanText;
      document.getElementById('d-no-peserta').textContent = p.no_peserta || '-';
      document.getElementById('d-perusahaan').textContent = p.perusahaan_pasien || '-';

      // Hide help, show detail
      if (helpCardsWrapper) helpCardsWrapper.style.display = 'none';
      patientDetailCard.style.display = 'block';
    }

    // Hide search input
    searchBox.style.display = 'none';
    dropdown.classList.remove('open');
    spinner.classList.remove('active');
  }

  // ── Clear selection ───────────────────────────────────────
  function clearSelection() {
    hiddenInput.value = '';
    hiddenInput.required = true;
    searchBox.value = '';
    searchBox.style.display = '';
    selectedCard.classList.remove('show');
    dropdown.classList.remove('open');
    dropdown.innerHTML = `<div class="dropdown-hint">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      Ketik minimal 2 karakter untuk mencari pasien
    </div>`;
    currentResults = [];

    // Hide detail, show help
    if (patientDetailCard) patientDetailCard.style.display = 'none';
    if (helpCardsWrapper) helpCardsWrapper.style.display = 'flex';

    searchBox.focus();
  }

  btnClear.addEventListener('click', clearSelection);

  // ── Search (debounced) ────────────────────────────────────
  function doSearch(q) {
    spinner.classList.add('active');
    fetch(`${SEARCH_URL}?q=${encodeURIComponent(q)}`, {
      headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
      spinner.classList.remove('active');
      renderDropdown(data);
    })
    .catch(() => {
      spinner.classList.remove('active');
      dropdown.innerHTML = '<div class="dropdown-empty">Terjadi kesalahan. Coba lagi.</div>';
      dropdown.classList.add('open');
    });
  }

  searchBox.addEventListener('input', function () {
    const q = this.value.trim();
    clearTimeout(debounceTimer);

    if (q.length < 2) {
      dropdown.innerHTML = `<div class="dropdown-hint">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        Ketik minimal 2 karakter untuk mencari pasien
      </div>`;
      dropdown.classList.add('open');
      spinner.classList.remove('active');
      return;
    }

    debounceTimer = setTimeout(() => doSearch(q), 280);
  });

  // ── Keyboard navigation ───────────────────────────────────
  searchBox.addEventListener('keydown', function (e) {
    const items = dropdown.querySelectorAll('.patient-dropdown-item');
    if (e.key === 'ArrowDown') {
      e.preventDefault();
      highlightIndex = Math.min(highlightIndex + 1, items.length - 1);
    } else if (e.key === 'ArrowUp') {
      e.preventDefault();
      highlightIndex = Math.max(highlightIndex - 1, 0);
    } else if (e.key === 'Enter' && highlightIndex >= 0 && currentResults[highlightIndex]) {
      e.preventDefault();
      selectPatient(currentResults[highlightIndex]);
      return;
    } else if (e.key === 'Escape') {
      dropdown.classList.remove('open');
      return;
    }
    items.forEach((el, i) => el.classList.toggle('highlighted', i === highlightIndex));
    if (items[highlightIndex]) items[highlightIndex].scrollIntoView({ block: 'nearest' });
  });

  // Show dropdown on focus if has text
  searchBox.addEventListener('focus', function () {
    if (this.value.trim().length >= 2) {
      dropdown.classList.add('open');
    }
  });

  // ── Close dropdown when clicking outside ──────────────────
  document.addEventListener('click', function (e) {
    if (!wrapper.contains(e.target)) {
      dropdown.classList.remove('open');
    }
  });

  // ── Form submit guard ─────────────────────────────────────
  document.getElementById('form-daftar').addEventListener('submit', function (e) {
    if (!hiddenInput.value) {
      e.preventDefault();
      searchBox.focus();
      searchBox.style.borderColor = '#ef4444';
      setTimeout(() => searchBox.style.borderColor = '', 2000);
      alert('Silakan pilih pasien terlebih dahulu.');
    }
  });

  // ── Auto-select patient from query parameters if exists ──────────────────
  @if(isset($selectedPasien) && $selectedPasien)
  const preselected = @json($selectedPasien);
  selectPatient(preselected);
  @endif
})();
</script>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
  new TomSelect('select[name="kd_poli"]', {
    create: false,
    sortField: {
      field: "text",
      direction: "asc"
    }
  });
  new TomSelect('select[name="kd_dokter"]', {
    create: false,
    sortField: {
      field: "text",
      direction: "asc"
    }
  });
});
</script>
@endpush

@endsection
