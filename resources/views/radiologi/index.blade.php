@extends('layout.app')

@section('title', 'Antrian Radiologi')
@section('page-title', 'Radiologi – Antrian Permintaan')

@section('content')
<!-- Tabs -->
<div class="tabs mb-20">
    <a href="{{ url('/radiologi?tab=antrian') }}" class="tab-btn {{ $tab === 'antrian' ? 'active' : '' }}">
        Antrian Baru
    </a>
    <a href="{{ url('/radiologi?tab=hasil') }}" class="tab-btn {{ $tab === 'hasil' ? 'active' : '' }}">
        Hasil Pemeriksaan
    </a>
</div>

<div class="card">
    <div class="card-header" style="justify-content: space-between; display: flex; align-items: center;">
        <h3 class="card-title">
            {{ $tab === 'antrian' ? 'Daftar Antrian Permintaan' : 'Riwayat Hasil Pemeriksaan' }}
        </h3>
        
        <form action="{{ url('/radiologi') }}" method="GET" style="display: flex; gap: 8px;">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <div class="input-group" style="width: 300px;">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari Nama/RM/No Order..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary btn-sm">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </button>
            </div>
        </form>
    </div>

    <div class="table-wrapper">
        <table class="table-hover table-nowrap">
            <thead>
                <tr>
                    <th>Waktu {{ $tab === 'antrian' ? 'Order' : 'Periksa' }}</th>
                    @if($tab === 'antrian') <th>No. Order</th> @endif
                    <th>No. Rawat</th>
                    <th>Nama Pasien</th>
                    <th>{{ $tab === 'antrian' ? 'Dokter Perujuk' : 'Hasil Tindakan' }}</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $item)
                <tr>
                    <td>
                        <div class="fw-semibold">{{ $item->tgl_permintaan }}</div>
                        <div class="text-muted small">{{ $item->jam_permintaan }}</div>
                    </td>
                    @if($tab === 'antrian') <td><span class="badge badge-blue">{{ $item->noorder }}</span></td> @endif
                    <td><span class="text-muted">{{ $item->no_rawat }}</span></td>
                    <td><span class="fw-bold">{{ $item->nm_pasien }}</span></td>
                    <td class="text-muted">
                        @if($tab === 'antrian')
                            {{ $item->dokter_pengirim }}
                        @else
                            <span class="text-primary fw-semibold">{{ $item->nm_perawatan }}</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="btn-group" style="gap:5px;">
                            @if($tab === 'antrian')
                            <a href="{{ url('/radiologi/periksa/'.$item->noorder) }}" class="btn btn-primary btn-sm">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                Proses
                            </a>
                            @else
                            <a href="{{ url('/radiologi/input/'.urlencode($item->no_rawat).'/'.urlencode($item->tgl_permintaan).'/'.urlencode($item->jam_permintaan)) }}" class="btn btn-teal btn-sm">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                Expertise
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-40">
                        <div class="text-muted">Belum ada data di tab ini.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($data->hasPages())
    <div class="card-footer" style="padding: 20px; display: flex; justify-content: center;">
        {{ $data->links() }}
    </div>
    @endif
</div>
@endsection
