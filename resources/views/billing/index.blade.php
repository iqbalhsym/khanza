@extends('layout.app')

@section('title', 'Kasir / Billing')
@section('page-title', 'Antrian Pembayaran')
@section('breadcrumb', 'Kasir Rawat Jalan')

@section('content')

@if(session('success'))
<div style="margin-bottom:16px;padding:14px 18px;background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.4);border-radius:10px;color:#047857;display:flex;align-items:center;gap:10px;">
  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
  {{ session('success') }}
</div>
@endif

<div class="page-header">
  <div class="page-header-left">
    <h1>Antrian Billing Pasien</h1>
    <p>Daftar pasien yang sudah selesai periksa dan menunggu pembayaran</p>
  </div>
</div>

<div class="card">
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>No. Rawat</th>
                    <th>Jam Reg</th>
                    <th>Nama Pasien</th>
                    <th>Poliklinik</th>
                    <th>Dokter</th>
                    <th>Cara Bayar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($antrian as $a)
                <tr>
                    <td><strong class="text-primary">{{ $a->no_rawat }}</strong></td>
                    <td>{{ $a->jam_reg }}</td>
                    <td>
                        <div style="font-weight:600;">{{ $a->nm_pasien }}</div>
                        <div style="font-size:11px; color:var(--text-muted)">RM: {{ $a->no_rkm_medis }}</div>
                    </td>
                    <td><span class="badge badge-gray">{{ $a->nm_poli }}</span></td>
                    <td>{{ $a->nm_dokter }}</td>
                    <td><span class="badge badge-blue">{{ $a->kd_pj }}</span></td>
                    <td>
                        <a href="{{ url('/billing/show/' . urlencode($a->no_rawat)) }}" class="btn btn-primary btn-sm">Proses Bayar</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center; padding:40px; color:var(--text-muted)">Tidak ada antrian pembayaran saat ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
