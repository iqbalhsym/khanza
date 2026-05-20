@extends('layout.app')

@section('title', 'Laporan')
@section('page-title', 'Laporan')

@section('content')

<div class="page-header">
  <div class="page-header-left">
    <h1>Laporan</h1>
    <p>Cetak dan export laporan operasional rumah sakit</p>
  </div>
  <div class="btn-group">
    <button class="btn btn-ghost btn-sm">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
      Export Excel
    </button>
    <button class="btn btn-primary btn-sm">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
      Cetak PDF
    </button>
  </div>
</div>

<!-- Filter Tanggal -->
<div class="card mb-16">
  <div class="card-body">
    <div class="filter-bar" style="margin-bottom:0;">
      <div class="form-group" style="margin-bottom:0;">
        <label class="form-label">Dari Tanggal</label>
        <input type="date" class="form-control" value="{{ date('Y-m-01') }}">
      </div>
      <div class="form-group" style="margin-bottom:0;">
        <label class="form-label">Sampai Tanggal</label>
        <input type="date" class="form-control" value="{{ date('Y-m-d') }}">
      </div>
      <div class="form-group" style="margin-bottom:0;">
        <label class="form-label">Jenis Laporan</label>
        <select class="form-control" style="width:220px;">
          <option>Kunjungan Pasien</option>
          <option>Pendapatan</option>
          <option>Rawat Inap</option>
          <option>Farmasi</option>
          <option>Laboratorium</option>
          <option>10 Besar Penyakit</option>
        </select>
      </div>
      <div style="align-self:flex-end;">
        <button class="btn btn-primary">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          Tampilkan
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Ringkasan Bulan Ini -->
<div class="stat-grid" style="margin-bottom:18px;">
  <div class="stat-card blue"><div class="stat-icon blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="22" height="22"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg></div><div class="stat-info"><div class="stat-label">Total Kunjungan</div><div class="stat-value">2.841</div><div class="stat-trend up">Maret 2026</div></div></div>
  <div class="stat-card green"><div class="stat-icon green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="22" height="22"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg></div><div class="stat-info"><div class="stat-label">Total Pendapatan</div><div class="stat-value" style="font-size:19px;">Rp 384 Jt</div><div class="stat-trend up">Maret 2026</div></div></div>
  <div class="stat-card orange"><div class="stat-icon orange"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="22" height="22"><path d="M3 9v12"/><path d="M21 9v12"/><path d="M3 13h18"/><rect x="7" y="4" width="10" height="5" rx="1"/></svg></div><div class="stat-info"><div class="stat-label">Hari Rawat</div><div class="stat-value">987</div><div class="stat-trend">BOR 72%</div></div></div>
  <div class="stat-card teal"><div class="stat-icon teal"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="22" height="22"><path d="M18.88 1.56a5 5 0 00-7.07 0L1.56 11.81a5 5 0 000 7.07l3.56 3.56a5 5 0 007.07 0l10.25-10.25a5 5 0 000-7.07z"/><line x1="9.5" y1="14.5" x2="14.5" y2="9.5"/></svg></div><div class="stat-info"><div class="stat-label">Resep Terlayani</div><div class="stat-value">1.654</div><div class="stat-trend">Maret 2026</div></div></div>
</div>

<!-- Laporan Grid -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;">

  <!-- 10 Besar Penyakit -->
  <div class="card">
    <div class="card-header">
      <h3>10 Besar Penyakit</h3>
      <span class="badge badge-blue">Bulan Ini</span>
    </div>
    <div class="table-wrapper">
      <table>
        <thead><tr><th>#</th><th>Diagnosa (ICD-10)</th><th>Jumlah</th><th>%</th></tr></thead>
        <tbody>
          @php $penyakit = [
            ['nama'=>'ISPA (J06)','jml'=>312],['nama'=>'Hipertensi (I10)','jml'=>287],
            ['nama'=>'DM Type 2 (E11)','jml'=>198],['nama'=>'Dyspepsia (K30)','jml'=>176],
            ['nama'=>'Diare (A09)','jml'=>143],['nama'=>'Gastroenteritis (A09.9)','jml'=>118],
            ['nama'=>'Anemia (D64)','jml'=>97],['nama'=>'Dermatitis (L30)','jml'=>85],
            ['nama'=>'Asma (J45)','jml'=>74],['nama'=>'Pneumonia (J18)','jml'=>62],
          ]; $total=array_sum(array_column($penyakit,'jml')); @endphp
          @foreach($penyakit as $i=>$p)
          <tr>
            <td class="text-muted fw-semibold">{{ $i+1 }}</td>
            <td class="fw-semibold">{{ $p['nama'] }}</td>
            <td class="fw-bold text-primary">{{ $p['jml'] }}</td>
            <td>
              <div style="display:flex;align-items:center;gap:8px;">
                <div style="flex:1;background:var(--bg);border-radius:10px;height:6px;min-width:60px;">
                  <div style="background:var(--accent);height:100%;border-radius:10px;width:{{ round(($p['jml']/$total)*100) }}%;"></div>
                </div>
                <span class="text-xs text-muted">{{ round(($p['jml']/$total)*100,1) }}%</span>
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  <!-- Kunjungan per Poli -->
  <div class="card">
    <div class="card-header">
      <h3>Kunjungan per Poliklinik</h3>
      <span class="badge badge-blue">Bulan Ini</span>
    </div>
    <div class="card-body">
      @php $poliData = [
        ['nama'=>'Poli Umum','jml'=>684,'pct'=>80],
        ['nama'=>'Poli Penyakit Dalam','jml'=>521,'pct'=>61],
        ['nama'=>'Poli Anak','jml'=>398,'pct'=>47],
        ['nama'=>'Poli Kandungan','jml'=>312,'pct'=>37],
        ['nama'=>'Poli Bedah','jml'=>287,'pct'=>34],
        ['nama'=>'Poli Mata','jml'=>198,'pct'=>23],
        ['nama'=>'Poli THT','jml'=>156,'pct'=>18],
        ['nama'=>'Poli Kulit','jml'=>143,'pct'=>17],
        ['nama'=>'Poli Jantung','jml'=>87,'pct'=>10],
        ['nama'=>'Poli Saraf','jml'=>55,'pct'=>6],
      ]; @endphp
      <div style="display:flex;flex-direction:column;gap:12px;">
        @foreach($poliData as $pd)
        <div>
          <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
            <span class="text-sm fw-semibold">{{ $pd['nama'] }}</span>
            <span class="text-sm text-muted">{{ $pd['jml'] }}</span>
          </div>
          <div style="height:8px;background:var(--bg);border-radius:10px;overflow:hidden;">
            <div style="height:100%;width:{{ $pd['pct'] }}%;background:linear-gradient(90deg,var(--primary),var(--accent));border-radius:10px;transition:width 0.8s;"></div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>

</div>

@endsection
