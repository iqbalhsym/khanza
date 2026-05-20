@extends('layout.app')

@section('title', 'Master Data Poliklinik')
@section('page-title', 'Daftar Unit/Poli')
@section('breadcrumb', 'Master Data')

@section('content')

<div class="page-header">
  <div class="page-header-left">
    <h1>Data Poliklinik</h1>
    <p>Daftar unit layanan rawat jalan yang tersedia</p>
  </div>
</div>

<div class="card">
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Kode Poli</th>
                    <th>Nama Poliklinik</th>
                    <th>Registrasi</th>
                    <th>Lama</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $d)
                <tr>
                    <td><strong class="text-primary">{{ $d->kd_poli }}</strong></td>
                    <td style="font-weight:600;">{{ $d->nm_poli }}</td>
                    <td>Rp {{ number_format($d->registrasi, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($d->registrasilama, 0, ',', '.') }}</td>
                    <td>
                        @if($d->status == '1')
                            <span class="badge badge-success"><span class="badge-dot"></span>Aktif</span>
                        @else
                            <span class="badge badge-danger"><span class="badge-dot"></span>Non-Aktif</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection
