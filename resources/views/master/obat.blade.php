@extends('layout.app')

@section('title', 'Master Data Obat')
@section('page-title', 'Daftar Obat/Alkes')
@section('breadcrumb', 'Master Data')

@section('content')

<div class="page-header">
  <div class="page-header-left">
    <h1>Data Obat & Alkes</h1>
    <p>Inventori obat dan alat kesehatan yang aktif</p>
  </div>
</div>

<div class="card">
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Satuan</th>
                    <th style="text-align:right">Harga Ralan</th>
                    <th style="text-align:right">Kelas 1</th>
                    <th style="text-align:right">Utama/VIP</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $d)
                <tr>
                    <td><strong class="text-muted">{{ $d->kode_brng }}</strong></td>
                    <td style="font-weight:600;">{{ $d->nama_brng }}</td>
                    <td><span class="badge badge-gray">{{ $d->satuan }}</span></td>
                    <td style="text-align:right">Rp {{ number_format($d->ralan, 0, ',', '.') }}</td>
                    <td style="text-align:right">Rp {{ number_format($d->kelas1, 0, ',', '.') }}</td>
                    <td style="text-align:right">Rp {{ number_format($d->vip, 0, ',', '.') }}</td>
                    <td>
                        @if($d->status == '1')
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
