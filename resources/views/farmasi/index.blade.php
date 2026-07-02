@extends('layout.app')

@section('title', 'Apotek / Farmasi')
@section('page-title', 'Antrian Farmasi')
@section('breadcrumb', 'Manajemen Resep')

@section('content')

{{-- Alert status --}}
@if(session('success'))
<div style="margin-bottom:16px;padding:14px 18px;background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.4);border-radius:10px;color:#047857;display:flex;align-items:center;gap:10px;">
  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
  {{ session('success') }}
</div>
@endif

@if(session('error'))
<div style="margin-bottom:16px;padding:14px 18px;background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.4);border-radius:10px;color:#b91c1c;">
  {{ session('error') }}
</div>
@endif

<div class="page-header">
  <div class="page-header-left">
    <h1>Antrian Resep Hari Ini</h1>
    <p>Kelola penyerahan obat kepada pasien rawat jalan</p>
  </div>
</div>

<div class="card">
    <div class="table-wrapper">
        <table class="table table-nowrap">
            <thead>
                <tr>
                    <th>No. Resep</th>
                    <th>Jam</th>
                    <th>Pasien</th>
                    <th>Unit/Poli</th>
                    <th>Detail Obat</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($resep as $r)
                <tr>
                    <td><strong class="text-primary">{{ $r->no_resep }}</strong></td>
                    <td>{{ $r->jam_peresepan }}</td>
                    <td>
                        <div style="font-weight:600; color:var(--text-primary)">{{ $r->nm_pasien }}</div>
                        <div style="font-size:11px; color:var(--text-muted)">RM: {{ $r->no_rkm_medis }}</div>
                    </td>
                    <td>{{ $r->nm_poli }}</td>
                    <td>
                        <ul style="margin:0; padding:0; list-style:none; font-size:12px;">
                            @foreach($r->items as $item)
                                <li style="margin-bottom:4px;">
                                    • {{ $item->nama_brng }} ({{ $item->jml }}) <br>
                                    <em style="color:var(--text-muted)">{{ $item->aturan_pakai }}</em>
                                </li>
                            @endforeach
                        </ul>
                    </td>
                    <td>
                        @if($r->tgl_penyerahan == '0000-00-00' || $r->tgl_penyerahan == '1000-01-01' || empty($r->tgl_penyerahan))
                            <span class="badge badge-warning"><span class="badge-dot"></span>Menunggu</span>
                        @else
                            <span class="badge badge-success"><span class="badge-dot"></span>Selesai</span>
                            <div style="font-size:10px; margin-top:4px;">{{ $r->jam_penyerahan }}</div>
                        @endif
                    </td>
                    <td>
                        @if($r->tgl_penyerahan == '0000-00-00' || $r->tgl_penyerahan == '1000-01-01' || empty($r->tgl_penyerahan))
                        <div class="d-flex align-items-center gap-2">
                            <form action="{{ url('/farmasi/dispense/' . $r->no_resep) }}" method="POST" style="margin:0;">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-sm">Serahkan Obat</button>
                            </form>
                            <form action="{{ url('/farmasi/cancel/' . $r->no_resep) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan resep {{ $r->no_resep }}?')" style="margin:0;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm">Batalkan</button>
                            </form>
                        </div>
                        @else
                            <button class="btn btn-ghost btn-sm" disabled>Sudah Diserahkan</button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center; padding:40px; color:var(--text-muted)">Belum ada antrian resep hari ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
