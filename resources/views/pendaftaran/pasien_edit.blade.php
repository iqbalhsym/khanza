@extends('layout.app')

@section('title', 'Edit Data Pasien')
@section('page-title', 'Pendaftaran')
@section('breadcrumb', 'Edit Pasien')

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
    <h1>Edit Data Pasien</h1>
    <p>Isi formulir berikut untuk mengubah data pasien</p>
  </div>
  <a href="{{ url('/pendaftaran') }}" class="btn btn-ghost">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    Kembali
  </a>
</div>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:18px;">

  <!-- Form -->
  <div>
    <form action="{{ url('/pendaftaran/update/'.$pasien->no_rkm_medis) }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')

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
              <input type="text" class="form-control" name="no_rkm_medis" value="{{ $pasien->no_rkm_medis }}" style="background-color: var(--bg); cursor: not-allowed;" readonly required>
            </div>
            <div class="form-group">
              <label class="form-label">Nama Pasien <span class="form-required">*</span></label>
              <input type="text" class="form-control" name="nm_pasien"value="{{ $pasien->nm_pasien }}" required autofocus>
            </div>
            <div class="form-group">
              <label class="form-label">No. KTP</label>
              <input type="text" class="form-control" name="no_ktp"value="{{ $pasien->no_ktp }}">
            </div>
            <div class="form-group">
              <label class="form-label">Jenis Kelamin <span class="form-required">*</span></label>
              <select class="form-control" name="jk" required>
                <option value="" {{ $pasien->jk == '' ? 'selected' : '' }}>Pilih</option>
                <option value="L" {{ $pasien->jk == 'L' ? 'selected' : '' }}>Laki-laki (L)</option>
                <option value="P" {{ $pasien->jk == 'P' ? 'selected' : '' }}>Perempuan (P)</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Tempat Lahir</label>
              <input type="text" class="form-control" name="tmp_lahir"value="{{ $pasien->tmp_lahir }}">
            </div>
            <div class="form-group">
              <label class="form-label">Tanggal Lahir <span class="form-required">*</span></label>
              <input type="date" class="form-control" name="tgl_lahir" id="tgl_lahir" value="{{ $pasien->tgl_lahir }}" required>
            </div>
            <div class="form-group">
              <label class="form-label">Umur (Terhitung Otomatis)</label>
              <input type="text" class="form-control" name="umur" id="umur" value="{{ $pasien->umur }}" style="background-color: var(--bg); cursor: not-allowed;" readonly>
            </div>
            <div class="form-group">
              <label class="form-label">Golongan Darah</label>
              <select class="form-control" name="gol_darah">
                <option value="-" {{ $pasien->gol_darah == '-' ? 'selected' : '' }}>Pilih</option><option value="A" {{ $pasien->gol_darah == 'A' ? 'selected' : '' }}>A</option><option value="B" {{ $pasien->gol_darah == 'B' ? 'selected' : '' }}>B</option><option value="AB" {{ $pasien->gol_darah == 'AB' ? 'selected' : '' }}>AB</option><option value="O" {{ $pasien->gol_darah == 'O' ? 'selected' : '' }}>O</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Agama</label>
              <select class="form-control" name="agama">
                <option value="-" {{ $pasien->agama == '-' ? 'selected' : '' }}>Pilih Agama</option><option {{ $pasien->agama == 'ISLAM' ? 'selected' : '' }}>ISLAM</option><option {{ $pasien->agama == 'KRISTEN' ? 'selected' : '' }}>KRISTEN</option><option {{ $pasien->agama == 'KATOLIK' ? 'selected' : '' }}>KATOLIK</option><option {{ $pasien->agama == 'HINDU' ? 'selected' : '' }}>HINDU</option><option {{ $pasien->agama == 'BUDDHA' ? 'selected' : '' }}>BUDDHA</option><option {{ $pasien->agama == 'KONGHUCHU' ? 'selected' : '' }}>KONGHUCHU</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Status Nikah</label>
              <select class="form-control" name="stts_nikah">
                <option value="" {{ $pasien->stts_nikah == '' ? 'selected' : '' }}>Pilih Status</option>
                <option value="BELUM MENIKAH" {{ $pasien->stts_nikah == 'BELUM MENIKAH' ? 'selected' : '' }}>Belum Menikah</option>
                <option value="MENIKAH" {{ $pasien->stts_nikah == 'MENIKAH' ? 'selected' : '' }}>Menikah</option>
                <option value="JANDA" {{ $pasien->stts_nikah == 'JANDA' ? 'selected' : '' }}>Janda</option>
                <option value="DUDHA" {{ $pasien->stts_nikah == 'DUDHA' ? 'selected' : '' }}>Duda</option>
                <option value="JOMBLO" {{ $pasien->stts_nikah == 'JOMBLO' ? 'selected' : '' }}>Jomblo</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Pendidikan</label>
              <select class="form-control" name="pnd">
                <option value="" {{ $pasien->pnd == '' ? 'selected' : '' }}>Pilih</option>
                <option value="TS" {{ $pasien->pnd == 'TS' ? 'selected' : '' }}>Tidak Sekolah (TS)</option>
                <option value="TK" {{ $pasien->pnd == 'TK' ? 'selected' : '' }}>TK</option>
                <option value="SD" {{ $pasien->pnd == 'SD' ? 'selected' : '' }}>SD</option>
                <option value="SMP" {{ $pasien->pnd == 'SMP' ? 'selected' : '' }}>SMP</option>
                <option value="SMA" {{ $pasien->pnd == 'SMA' ? 'selected' : '' }}>SMA/SMK</option>
                <option value="D3" {{ $pasien->pnd == 'D3' ? 'selected' : '' }}>D3</option>
                <option value="D4" {{ $pasien->pnd == 'D4' ? 'selected' : '' }}>D4</option>
                <option value="S1" {{ $pasien->pnd == 'S1' ? 'selected' : '' }}>S1</option>
                <option value="S2" {{ $pasien->pnd == 'S2' ? 'selected' : '' }}>S2</option>
                <option value="S3" {{ $pasien->pnd == 'S3' ? 'selected' : '' }}>S3</option>
                <option value="-" {{ $pasien->pnd == '-' ? 'selected' : '' }}>Lainnya</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Pekerjaan</label>
              <input type="text" class="form-control" name="pekerjaan"value="{{ $pasien->pekerjaan }}">
            </div>
            <div class="form-group">
              <label class="form-label">Data Pendukung (Maks 2MB)</label>
              <input type="file" class="form-control" name="data_pendukung" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
              @if(isset($pasien->data_pendukung) && $pasien->data_pendukung)
                <div style="margin-top: 8px; font-size: 12px;">
                  <a href="{{ asset('uploads/data_pendukung/' . $pasien->data_pendukung) }}" target="_blank" style="color: var(--primary); font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    Lihat Dokumen saat ini
                  </a>
                </div>
              @endif
            </div>
            <div class="form-group">
              <label class="form-label">Special Precautions (Prioritas Pasien)</label>
              <input type="text" class="form-control" name="special_precautions" value="{{ $pasien->special_precautions ?? '' }}" placeholder="Contoh: Karyawan, Orang Penting, Biasa">
            </div>
            <div class="form-group">
              <label class="form-label">Warna Peringatan (Special Precautions Color)</label>
              <select class="form-control" name="special_precautions_color">
                <option value="" {{ ($pasien->special_precautions_color ?? '') == '' ? 'selected' : '' }}>Pilih Warna</option>
                <option value="green" {{ ($pasien->special_precautions_color ?? '') == 'green' ? 'selected' : '' }}>Hijau (Green)</option>
                <option value="yellow" {{ ($pasien->special_precautions_color ?? '') == 'yellow' ? 'selected' : '' }}>Kuning (Yellow)</option>
                <option value="red" {{ ($pasien->special_precautions_color ?? '') == 'red' ? 'selected' : '' }}>Merah (Red)</option>
              </select>
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
            <textarea class="form-control" name="alamat" rows="2">{{ $pasien->alamat }}</textarea>
          </div>
          <div class="form-grid form-grid-2">
            <div class="form-group">
              <label class="form-label">No. Telepon / HP</label>
              <input type="text" class="form-control" name="no_tlp"value="{{ $pasien->no_tlp }}">
            </div>
            <div class="form-group">
              <label class="form-label">Email</label>
              <input type="email" class="form-control" name="email"value="{{ $pasien->email }}">
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
              <input type="text" class="form-control" name="nm_ibu"value="{{ $pasien->nm_ibu }}" required>
            </div>
            <div class="form-group">
              <label class="form-label">Nama PJ (Penanggung Jawab)</label>
              <input type="text" class="form-control" name="namakeluarga"value="{{ $pasien->namakeluarga }}">
            </div>
            <div class="form-group">
              <label class="form-label">Hubungan dengan PJ</label>
              <select class="form-control" name="keluarga">
                <option value="" {{ $pasien->keluarga == '' ? 'selected' : '' }}>Pilih Hubungan</option>
                <option value="AYAH" {{ $pasien->keluarga == 'AYAH' ? 'selected' : '' }}>Ayah</option>
                <option value="IBU" {{ $pasien->keluarga == 'IBU' ? 'selected' : '' }}>Ibu</option>
                <option value="ISTRI" {{ $pasien->keluarga == 'ISTRI' ? 'selected' : '' }}>Istri</option>
                <option value="SUAMI" {{ $pasien->keluarga == 'SUAMI' ? 'selected' : '' }}>Suami</option>
                <option value="SAUDARA" {{ $pasien->keluarga == 'SAUDARA' ? 'selected' : '' }}>Saudara</option>
                <option value="ANAK" {{ $pasien->keluarga == 'ANAK' ? 'selected' : '' }}>Anak</option>
                <option value="DIRI SENDIRI" {{ $pasien->keluarga == 'DIRI SENDIRI' ? 'selected' : '' }}>Diri Sendiri</option>
                <option value="LAIN-LAIN" {{ $pasien->keluarga == 'LAIN-LAIN' ? 'selected' : '' }}>Lain-lain</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Pekerjaan PJ</label>
              <input type="text" class="form-control" name="pekerjaanpj"value="{{ $pasien->pekerjaanpj }}">
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Alamat PJ (Kosongkan jika sama dengan pasien)</label>
            <textarea class="form-control" name="alamatpj" rows="2">{{ $pasien->alamatpj }}</textarea>
          </div>
          <div class="form-grid form-grid-4">
             <div class="form-group">
              <label class="form-label">Provinsi PJ</label>
              <input type="text" class="form-control" name="propinsipj" value="{{ $pasien->propinsipj }}">
            </div>
            <div class="form-group">
              <label class="form-label">Kab/Kota PJ</label>
              <input type="text" class="form-control" name="kabupatenpj" value="{{ $pasien->kabupatenpj }}">
            </div>
            <div class="form-group">
              <label class="form-label">Kecamatan PJ</label>
              <input type="text" class="form-control" name="kecamatanpj" value="{{ $pasien->kecamatanpj }}">
            </div>
             <div class="form-group">
              <label class="form-label">Kelurahan PJ</label>
              <input type="text" class="form-control" name="kelurahanpj" value="{{ $pasien->kelurahanpj }}">
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
              <input type="date" class="form-control" name="tgl_daftar" value="{{ $pasien->tgl_daftar }}" readonly>
            </div>
             <div class="form-group">
              <label class="form-label">Kode Jaminan / KD PJ (BPJ/ASU/UMUM) <span class="form-required">*</span></label>
              <select class="form-control" name="kd_pj" required>
                <option value="BPJ" {{ $pasien->kd_pj == 'BPJ' ? 'selected' : '' }}>BPJS Kesehatan (BPJ)</option>
                <option value="A09" {{ $pasien->kd_pj == 'A09' ? 'selected' : '' }}>Umum / Mandiri (A09)</option>
                <option value="-" {{ $pasien->kd_pj == '-' ? 'selected' : '' }}>Lain-lain / Tanpa Jaminan (-)</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">No. Kartu Peserta (BPJS/Asuransi)</label>
              <input type="text" class="form-control" name="no_peserta"value="{{ $pasien->no_peserta }}">
            </div>
            <div class="form-group">
              <label class="form-label">Perusahaan Pasien</label>
              <input type="text" class="form-control" name="perusahaan_pasien"value="{{ $pasien->perusahaan_pasien }}">
            </div>
            <div class="form-group">
              <label class="form-label">NIP / NRP / Karyawan</label>
              <input type="text" class="form-control" name="nip"value="{{ $pasien->nip }}">
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

    <!-- Removed Nomor RM box for Edit Page -->
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
