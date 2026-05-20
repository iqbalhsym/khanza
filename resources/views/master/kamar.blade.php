@extends('layout.app')

@section('title', 'Master Data Kamar')
@section('page-title', 'Daftar Kamar & Bangsal')
@section('breadcrumb', 'Master Data')

@section('content')

<div class="page-header">
  <div class="page-header-left">
    <h1>Data Kamar Inap</h1>
    <p>Daftar kamar, bangsal, dan tarif layanan rawat inap</p>
  </div>
</div>

<div class="card">
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>No. Kamar</th>
                    <th>Bangsal / Ruangan</th>
                    <th>Kelas</th>
                    <th style="text-align:right">Tarif Kamar</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $d)
                <tr>
                    <td><strong class="text-primary">{{ $d->kd_kamar }}</strong></td>
                    <td style="font-weight:600;">{{ $d->nm_bangsal }}</td>
                    <td><span class="badge badge-blue">Kelas {{ $d->kelas }}</span></td>
                    <td style="text-align:right">Rp {{ number_format($d->trf_kamar, 0, ',', '.') }}</td>
                    <td>
                        @if($d->statusdata == '1')
                            <span class="badge badge-success">Aktif</span>
                        @else
                            <span class="badge badge-danger">Non-Aktif</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div style="padding: 15px; display: flex; justify-content: center;">
        {{ $data->links() }}
    </div>
</div>

@endsection
