@extends('layout.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="row row-title align-items-center mb-3">
  <div class="col">
    <h1 class="h2">Selamat Datang, Administrator 👋</h1>
    <p class="text-muted">Berikut ringkasan aktifitas rumah sakit hari ini, {{ date('d F Y') }}</p>
  </div>
  <div class="col-auto">
    <div class="btn-list">
      <a href="{{ url('/pendaftaran/pasien-baru') }}" class="btn btn-primary d-none d-sm-inline-block">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" /></svg>
        Daftar Pasien Baru
      </a>
      <button class="btn btn-outline-secondary d-none d-sm-inline-block">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><polyline points="7 11 12 16 17 11" /><line x1="12" y1="4" x2="12" y2="16" /></svg>
        Export
      </button>
    </div>
  </div>
</div>

<div class="row row-cards">
  <!-- Stat Card: Rawat Inap -->
  <div class="col-sm-6 col-lg-3">
    <div class="card card-sm dashboard-stat-card">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col-auto">
            <span class="bg-blue text-white avatar">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 7v11m0 -4h18m0 4v-11M7 7v11" /><path d="M7 7h10a2 2 0 0 1 2 2v2H5V9a2 2 0 0 1 2 -2" /></svg>
            </span>
          </div>
          <div class="col">
            <div class="font-weight-medium">
              {{ number_format($stats['ranap']) }} Pasien
            </div>
            <div class="text-muted">
              Rawat Inap
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Stat Card: Rawat Jalan -->
  <div class="col-sm-6 col-lg-3">
    <div class="card card-sm dashboard-stat-card">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col-auto">
            <span class="bg-green text-white avatar">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="5" width="16" height="16" rx="2" /><line x1="16" y1="3" x2="16" y2="7" /><line x1="8" y1="3" x2="8" y2="7" /><line x1="4" y1="11" x2="20" y2="11" /><line x1="11" y1="15" x2="12" y2="15" /><line x1="12" y1="15" x2="12" y2="18" /></svg>
            </span>
          </div>
          <div class="col">
            <div class="font-weight-medium">
              {{ number_format($stats['ralan']) }} Kunjungan
            </div>
            <div class="text-muted">
              Rawat Jalan
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Stat Card: Emergency -->
  <div class="col-sm-6 col-lg-3">
    <div class="card card-sm dashboard-stat-card">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col-auto">
            <span class="bg-red text-white avatar">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-activity" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12h4l3 8l4 -16l3 8h4" /></svg>
            </span>
          </div>
          <div class="col">
            <div class="font-weight-medium">
              {{ number_format($stats['emergency']) }} Pasien
            </div>
            <div class="text-muted">
              Emergency
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Stat Card: Konsul -->
  <div class="col-sm-6 col-lg-3">
    <div class="card card-sm dashboard-stat-card">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col-auto">
            <span class="bg-purple text-white avatar">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-messages" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M21 14l-3 -3h-7a1 1 0 0 1 -1 -1v-6a1 1 0 0 1 1 -1h9a1 1 0 0 1 1 1v10z" /><path d="M14 15v2a1 1 0 0 1 -1 1h-7l-3 3v-10a1 1 0 0 1 1 -1h2" /></svg>
            </span>
          </div>
          <div class="col">
            <div class="font-weight-medium">
              {{ number_format($stats['konsul']) }} Sesi
            </div>
            <div class="text-muted">
              Konsul
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row row-cards mt-1">
  <!-- Left Side: Pasien Ralan -->
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Antrian Teratas Rawat Jalan</h3>
        <div class="card-actions">
           <a href="{{ url('/rawat-jalan') }}" class="btn btn-outline-primary btn-sm">Lihat Semua</a>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table card-table table-vcenter text-nowrap datatable">
          <thead>
            <tr>
              <th>Nama Pasien</th>
              <th>Poliklinik</th>
              <th>Dokter</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @foreach($recent_activities as $ra)
            <tr>
              <td>
                <div class="d-flex py-1 align-items-center">
                  <div class="flex-fill">
                    <div class="font-weight-medium">{{ $ra->nm_pasien }}</div>
                    <div class="text-muted small">{{ $ra->no_rkm_medis }}</div>
                  </div>
                </div>
              </td>
              <td>{{ $ra->nm_poli }}</td>
              <td class="text-muted">{{ $ra->nm_dokter }}</td>
              <td>
                @php
                  $status_type = $ra->stts == 'Belum' ? 'warning' : ($ra->stts == 'Sudah' ? 'success' : 'secondary');
                @endphp
                <span class="badge bg-{{ $status_type }}-lt">{{ $ra->stts }}</span>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Right Side: Recent Activity -->
  <div class="col-lg-4">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Aktivitas Terkini</h3>
      </div>
      <div class="card-body">
        <ul class="steps steps-vertical">
          <li class="step-item">
            <div class="h4 m-0">Pasien RM-2024-006 Selesai</div>
            <div class="text-muted small">Poli Kandungan – dr. Fitri</div>
            <div class="text-muted small">10:45 WIB</div>
          </li>
          <li class="step-item">
            <div class="h4 m-0">Resep baru dari dr. Budi</div>
            <div class="text-muted small">Farmasi – 3 item obat</div>
            <div class="text-muted small">10:32 WIB</div>
          </li>
          <li class="step-item active">
            <div class="h4 m-0">Pasien baru masuk Rawat Inap</div>
            <div class="text-muted small">Kelas II – Kamar 215</div>
            <div class="text-muted small">10:15 WIB</div>
          </li>
          <li class="step-item">
            <div class="h4 m-0">Hasil Lab siap</div>
            <div class="text-muted small">Pasien RM-2024-001</div>
            <div class="text-muted small">09:58 WIB</div>
          </li>
        </ul>
      </div>
    </div>

    <!-- Bed Capacity -->
    <div class="card mt-3">
      <div class="card-header">
        <h3 class="card-title">Ketersediaan Bed</h3>
      </div>
      <div class="card-body">
         @php
          $bangsal = [
            ['nama'=>'VVIP/VIP','total'=>10,'terisi'=>8],
            ['nama'=>'Kelas I','total'=>20,'terisi'=>15],
            ['nama'=>'Kelas II','total'=>30,'terisi'=>18],
            ['nama'=>'Kelas III','total'=>40,'terisi'=>12]
          ];
        @endphp
        @foreach($bangsal as $b)
        @php $pct = round(($b['terisi']/$b['total'])*100); @endphp
        <div class="mb-3">
          <div class="d-flex mb-1">
            <div>{{ $b['nama'] }}</div>
            <div class="ms-auto">
              <span class="text-muted">{{ $b['terisi'] }}/{{ $b['total'] }}</span>
            </div>
          </div>
          <div class="progress progress-sm">
            <div class="progress-bar bg-{{ $pct > 80 ? 'danger' : 'primary' }}" style="width: {{ $pct }}%" role="progressbar" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100">
            </div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
</div>
@endsection
