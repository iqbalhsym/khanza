@extends('layout.app')

@section('title', 'Master Data Dokter')
@section('page-title', 'Daftar Dokter')
@section('breadcrumb', 'Master Data')

@section('content')

<div class="page-header">
  <div class="page-header-left">
    <h1>Data Dokter</h1>
    <p>Manajemen dokter yang aktif melayani pasien</p>
  </div>
</div>

<div class="card">
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Kode Dokter</th>
                    <th>Nama Dokter</th>
                    <th>Spesialis</th>
                    <th>No. Ijin Praktek</th>
                    <th>Alamat</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $d)
                <tr>
                    <td><strong class="text-primary">{{ $d->kd_dokter }}</strong></td>
                    <td style="font-weight:600;">{{ $d->nm_dokter }}</td>
                    <td><span class="badge badge-blue">{{ $d->nm_sps }}</span></td>
                    <td>{{ $d->no_ijn_praktek }}</td>
                    <td style="font-size:12px;">{{ $d->almt_tgl }}</td>
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
