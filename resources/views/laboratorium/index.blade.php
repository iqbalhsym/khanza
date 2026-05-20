@extends('layout.app')

@section('title', 'Antrian Laboratorium')
@section('page-title', 'Laboratorium – Antrian Permintaan')

@section('content')
<!-- Tabs -->
<div class="tabs mb-20">
    <a href="{{ url('/laboratorium?tab=antrian') }}" class="tab-btn {{ $tab === 'antrian' ? 'active' : '' }}">
        Antrian Baru
    </a>
    <a href="{{ url('/laboratorium?tab=hasil') }}" class="tab-btn {{ $tab === 'hasil' ? 'active' : '' }}">
        Hasil Selesai
    </a>
</div>

<div class="card">
    <div class="card-header" style="justify-content: space-between; display: flex; align-items: center;">
        <h3 class="card-title">
            {{ $tab === 'antrian' ? 'Daftar Antrian Permintaan Lab' : 'Riwayat Hasil Laboratorium' }}
        </h3>
        
        <form action="{{ url('/laboratorium') }}" method="GET" style="display: flex; gap: 8px;">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <div class="input-group" style="width: 300px;">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari Nama/RM/No Rawat..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary btn-sm">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </button>
            </div>
        </form>
    </div>

    <div class="table-wrapper">
        <table class="table-hover">
            <thead>
                <tr>
                    <th>Waktu {{ $tab === 'antrian' ? 'Order' : 'Periksa' }}</th>
                    @if($tab === 'antrian') <th>No. Order</th> @endif
                    <th>No. Rawat</th>
                    <th>Nama Pasien</th>
                    <th>{{ $tab === 'antrian' ? 'Dokter Perujuk' : 'Dokter Pemeriksa' }}</th>
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
                    <td class="text-muted">{{ $item->dokter_pengirim }}</td>
                    <td class="text-center">
                        <div class="btn-group" style="gap:5px;">
                            @if($tab === 'antrian')
                            <a href="{{ url('/laboratorium/input/'.$item->noorder) }}" class="btn btn-primary btn-sm">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                Input Hasil
                            </a>
                            @else
                            <a href="{{ url('/laboratorium/view-hasil/'.urlencode($item->no_rawat).'/'.urlencode($item->tgl_permintaan).'/'.urlencode($item->jam_permintaan)) }}" class="btn btn-teal btn-sm">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                Lihat Hasil
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-40">
                        <div class="text-muted mb-8">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" style="opacity: 0.2; margin-bottom: 12px;">
                                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/>
                            </svg>
                        </div>
                        <div class="fw-semibold text-muted">Belum ada permintaan laboratorium.</div>
                        <div class="text-muted small">Daftar akan terisi jika ada rujukan lab dari poli/bangsal.</div>
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
