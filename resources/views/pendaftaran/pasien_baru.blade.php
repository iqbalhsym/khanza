@extends('layout.app')

@section('title', 'Pendaftaran Pasien Baru')
@section('page-title', 'Pendaftaran')
@section('breadcrumb', 'Pasien Baru')

@section('content')

{{-- Alert sukses --}}
@if(session('success'))
<div style="margin-bottom:16px;padding:14px 18px;background:rgba(34,197,94,0.12);border:1px solid rgba(34,197,94,0.4);border-radius:10px;color:#15803d;display:flex;align-items:center;gap:10px;">
  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
  {{ session('success') }}
</div>
@endif

{{-- Alert error umum --}}
@if(session('error'))
<div style="margin-bottom:16px;padding:14px 18px;background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.4);border-radius:10px;color:#b91c1c;display:flex;align-items:center;gap:10px;">
  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
  {{ session('error') }}
</div>
@endif

{{-- Validasi errors --}}
@if($errors->any())
<div style="margin-bottom:16px;padding:14px 18px;background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.4);border-radius:10px;color:#b91c1c;">
  <strong>Terdapat kesalahan pada form:</strong>
  <ul style="margin:6px 0 0 18px;">
    @foreach($errors->all() as $error)
      <li>{{ $error }}</li>
    @endforeach
  </ul>
</div>
@endif

<div class="page-header">
  <div class="page-header-left">
    <h1>Pendaftaran Pasien Baru</h1>
    <p>Isi formulir berikut untuk mendaftarkan pasien baru</p>
  </div>
  <a href="{{ url('/pendaftaran') }}" class="btn btn-ghost">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    Kembali
  </a>
</div>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:18px;">

  <!-- Form -->
  <div>
    <form action="{{ url('/pendaftaran/store') }}" method="POST" enctype="multipart/form-data">
      @csrf

      <!-- 1. Data Pribadi -->
      <div class="card mb-16">
        <div class="card-header">
          <h3>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" style="display:inline;vertical-align:-3px;margin-right:6px;"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            1. Data Pribadi
          </h3>
        </div>
        <div class="card-body">
          <div class="form-grid form-grid-2">
            <div class="form-group">
              <label class="form-label">No. Rekam Medis <span class="form-required">*</span></label>
              <input type="text" class="form-control" name="no_rkm_medis" value="{{ $nextRm }}" style="background-color: var(--bg); cursor: not-allowed;" readonly required>
            </div>
            <div class="form-group">
              <label class="form-label">Nama Pasien <span class="form-required">*</span></label>
              <input type="text" class="form-control" name="nm_pasien" placeholder="Nama lengkap sesuai KTP" required autofocus>
            </div>
            <div class="form-group">
              <label class="form-label">No. KTP</label>
              <input type="text" class="form-control" name="no_ktp" placeholder="16 digit NIK">
            </div>
            <div class="form-group">
              <label class="form-label">Jenis Kelamin <span class="form-required">*</span></label>
              <select class="form-control" name="jk" required>
                <option value="">Pilih</option>
                <option value="L">Laki-laki (L)</option>
                <option value="P">Perempuan (P)</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Tempat Lahir</label>
              <input type="text" class="form-control" name="tmp_lahir" placeholder="Kota kelahiran">
            </div>
            <div class="form-group">
              <label class="form-label">Tanggal Lahir <span class="form-required">*</span></label>
              <input type="date" class="form-control" name="tgl_lahir" id="tgl_lahir" required>
            </div>
            <div class="form-group">
              <label class="form-label">Umur (Terhitung Otomatis)</label>
              <input type="text" class="form-control" name="umur" id="umur" placeholder="Otomatis dari Tgl Lahir" style="background-color: var(--bg); cursor: not-allowed;" readonly>
            </div>
            <div class="form-group">
              <label class="form-label">Golongan Darah</label>
              <select class="form-control" name="gol_darah">
                <option value="-">Pilih</option><option value="A">A</option><option value="B">B</option><option value="AB">AB</option><option value="O">O</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Agama</label>
              <select class="form-control" name="agama">
                <option value="-">Pilih Agama</option><option>ISLAM</option><option>KRISTEN</option><option>KATOLIK</option><option>HINDU</option><option>BUDDHA</option><option>KONGHUCHU</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Status Nikah</label>
              <select class="form-control" name="stts_nikah">
                <option value="">Pilih Status</option>
                <option value="BELUM MENIKAH">Belum Menikah</option>
                <option value="MENIKAH">Menikah</option>
                <option value="JANDA">Janda</option>
                <option value="DUDHA">Duda</option>
                <option value="JOMBLO">Jomblo</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Pendidikan</label>
              <select class="form-control" name="pnd">
                <option value="">Pilih</option>
                <option value="TS">Tidak Sekolah (TS)</option>
                <option value="TK">TK</option>
                <option value="SD">SD</option>
                <option value="SMP">SMP</option>
                <option value="SMA">SMA/SMK</option>
                <option value="D3">D3</option>
                <option value="D4">D4</option>
                <option value="S1">S1</option>
                <option value="S2">S2</option>
                <option value="S3">S3</option>
                <option value="-">Lainnya</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Pekerjaan</label>
              <input type="text" class="form-control" name="pekerjaan" placeholder="Pekerjaan pasien">
            </div>
            <div class="form-group">
              <label class="form-label">Data Pendukung (Maks 2MB)</label>
              <input type="file" class="form-control" name="data_pendukung" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
            </div>
          </div>
        </div>
      </div>

      <!-- 2. Kontak & Alamat -->
      <div class="card mb-16">
        <div class="card-header">
          <h3>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" style="display:inline;vertical-align:-3px;margin-right:6px;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
            2. Kontak & Alamat
          </h3>
        </div>
        <div class="card-body">
          <div class="form-group">
            <label class="form-label">Alamat Lengkap</label>
            <textarea class="form-control" name="alamat" rows="2" placeholder="Nama jalan, gang, RT/RW"></textarea>
          </div>
          <div class="form-grid form-grid-2">
            <div class="form-group">
              <label class="form-label">No. Telepon / HP</label>
              <input type="text" class="form-control" name="no_tlp" placeholder="0812xxxxxx">
            </div>
            <div class="form-group">
              <label class="form-label">Email</label>
              <input type="email" class="form-control" name="email" placeholder="contoh@rsui.ac.id">
            </div>
          </div>
        </div>
      </div>

      <!-- 3. Keluarga & Penanggung Jawab -->
      <div class="card mb-16">
        <div class="card-header">
          <h3>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" style="display:inline;vertical-align:-3px;margin-right:6px;"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
            3. Penanggung Jawab & Keluarga
          </h3>
        </div>
        <div class="card-body">
          <div class="form-grid form-grid-2">
            <div class="form-group">
              <label class="form-label">Nama Ibu Kandung <span class="form-required">*</span></label>
              <input type="text" class="form-control" name="nm_ibu" placeholder="Nama ibu pasien" required>
            </div>
            <div class="form-group">
              <label class="form-label">Nama PJ (Penanggung Jawab)</label>
              <input type="text" class="form-control" name="namakeluarga" placeholder="Nama penanggung jawab">
            </div>
            <div class="form-group">
              <label class="form-label">Hubungan dengan PJ</label>
              <select class="form-control" name="keluarga">
                <option value="">Pilih Hubungan</option>
                <option value="AYAH">Ayah</option>
                <option value="IBU">Ibu</option>
                <option value="ISTRI">Istri</option>
                <option value="SUAMI">Suami</option>
                <option value="SAUDARA">Saudara</option>
                <option value="ANAK">Anak</option>
                <option value="DIRI SENDIRI">Diri Sendiri</option>
                <option value="LAIN-LAIN">Lain-lain</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Pekerjaan PJ</label>
              <input type="text" class="form-control" name="pekerjaanpj" placeholder="Pekerjaan penanggung jawab">
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Alamat PJ (Kosongkan jika sama dengan pasien)</label>
            <textarea class="form-control" name="alamatpj" rows="2" placeholder="Alamat lengkap PJ"></textarea>
          </div>
          <div class="form-grid form-grid-4">
             <div class="form-group">
              <label class="form-label">Provinsi PJ</label>
              <input type="text" class="form-control" name="propinsipj">
            </div>
            <div class="form-group">
              <label class="form-label">Kab/Kota PJ</label>
              <input type="text" class="form-control" name="kabupatenpj">
            </div>
            <div class="form-group">
              <label class="form-label">Kecamatan PJ</label>
              <input type="text" class="form-control" name="kecamatanpj">
            </div>
             <div class="form-group">
              <label class="form-label">Kelurahan PJ</label>
              <input type="text" class="form-control" name="kelurahanpj">
            </div>
          </div>
        </div>
      </div>

      <!-- 4. Registrasi & Asuransi -->
      <div class="card mb-16">
        <div class="card-header">
          <h3>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" style="display:inline;vertical-align:-3px;margin-right:6px;"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/></svg>
            4. Registrasi & Jaminan
          </h3>
        </div>
        <div class="card-body">
          <div class="form-grid form-grid-2">
            <div class="form-group">
              <label class="form-label">Tanggal Daftar</label>
              <input type="date" class="form-control" name="tgl_daftar" value="{{ date('Y-m-d') }}" readonly>
            </div>
             <div class="form-group">
              <label class="form-label">Kode Jaminan / KD PJ (BPJ/ASU/UMUM) <span class="form-required">*</span></label>
              <select class="form-control" name="kd_pj" required>
                <option value="BPJ">BPJS Kesehatan (BPJ)</option>
                <option value="A09">Umum / Mandiri (A09)</option>
                <option value="-">Lain-lain / Tanpa Jaminan (-)</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">No. Kartu Peserta (BPJS/Asuransi)</label>
              <input type="text" class="form-control" name="no_peserta" placeholder="Nomor peserta asuransi">
            </div>
            <div class="form-group">
              <label class="form-label">Perusahaan Pasien</label>
              <input type="text" class="form-control" name="perusahaan_pasien" placeholder="Instansi / Perusahaan domisili">
            </div>
            <div class="form-group">
              <label class="form-label">NIP / NRP / Karyawan</label>
              <input type="text" class="form-control" name="nip" placeholder="Nomor identitas pegawai">
            </div>
          </div>
        </div>
      </div>

      <!-- Tombol -->
      <div class="btn-group" style="justify-content:flex-end;">
        <a href="{{ url('/pendaftaran') }}" class="btn btn-ghost">Batal</a>
        <button type="reset" class="btn btn-outline">Reset Form</button>
        <button type="submit" class="btn btn-primary">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
          Simpan Data Pasien
        </button>
      </div>
    </form>
  </div>

  <!-- Sidebar Info -->
  <div style="display:flex;flex-direction:column;gap:16px;">

    <div class="card">
      <div class="card-header"><h3>Panduan Pendaftaran</h3></div>
      <div class="card-body text-sm" style="padding:16px;">
        <div style="display:flex;flex-direction:column;gap:10px;">
          <div style="display:flex;gap:10px;align-items:flex-start;">
            <span style="width:22px;height:22px;background:var(--primary);color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;">1</span>
            <span>Isi data pribadi pasien sesuai KTP</span>
          </div>
          <div style="display:flex;gap:10px;align-items:flex-start;">
            <span style="width:22px;height:22px;background:var(--primary);color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;">2</span>
            <span>Pilih poliklinik dan dokter yang dituju</span>
          </div>
          <div style="display:flex;gap:10px;align-items:flex-start;">
            <span style="width:22px;height:22px;background:var(--primary);color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;">3</span>
            <span>Pilih jenis jaminan kesehatan</span>
          </div>
          <div style="display:flex;gap:10px;align-items:flex-start;">
            <span style="width:22px;height:22px;background:var(--accent);color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;">4</span>
            <span>Klik simpan untuk cetak nomor antrian</span>
          </div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header"><h3>Nomor RM Berikutnya</h3></div>
      <div class="card-body" style="text-align:center;padding:20px;">
        <div style="font-size:28px;font-weight:800;color:var(--primary);letter-spacing:-1px;">{{ $nextRm }}</div>
        <div class="text-sm text-muted mt-4">Nomor rekam medis berikutnya</div>
      </div>
    </div>

    <div class="card">
      <div class="card-header"><h3>Jadwal Dokter Hari Ini</h3></div>
      <div class="card-body" style="padding:12px;">
        @php $jadwal = [
          ['dokter'=>'dr. Budi, SpPD','poli'=>'Poli Umum','jam'=>'08:00-12:00'],
          ['dokter'=>'dr. Rina, SpA','poli'=>'Poli Anak','jam'=>'09:00-13:00'],
          ['dokter'=>'dr. Hasan, SpB','poli'=>'Poli Bedah','jam'=>'10:00-14:00'],
          ['dokter'=>'dr. Fitri, SpOG','poli'=>'Kandungan','jam'=>'08:00-11:00'],
        ]; @endphp
        @foreach($jadwal as $j)
        <div style="padding:8px;border-bottom:1px solid var(--border);">
          <div style="font-size:12.5px;font-weight:600;">{{ $j['dokter'] }}</div>
          <div style="font-size:11px;color:var(--text-muted);">{{ $j['poli'] }} • {{ $j['jam'] }}</div>
        </div>
        @endforeach
      </div>
    </div>

  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tglLahirInput = document.getElementById('tgl_lahir');
    const umurInput = document.getElementById('umur');

    if(tglLahirInput && umurInput) {
        tglLahirInput.addEventListener('change', function() {
            if(!this.value) {
                umurInput.value = '';
                return;
            }

            const birthDate = new Date(this.value);
            const today = new Date();
            
            let years = today.getFullYear() - birthDate.getFullYear();
            let months = today.getMonth() - birthDate.getMonth();
            let days = today.getDate() - birthDate.getDate();

            if (months < 0 || (months === 0 && days < 0)) {
                years--;
                months += 12;
            }
            if (days < 0) {
                const prevMonth = new Date(today.getFullYear(), today.getMonth(), 0);
                days += prevMonth.getDate();
                months--;
            }

            umurInput.value = `${years} Th ${months} Bl ${days} Hr`;
        });
        
        // Trigger calculation on load if there's an existing value
        if(tglLahirInput.value) {
            tglLahirInput.dispatchEvent(new Event('change'));
        }
    }
});
</script>

@endsection
