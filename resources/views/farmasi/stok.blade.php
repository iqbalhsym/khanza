@extends('layout.app')

@section('title', 'Stok Obat & Alkes')
@section('page-title', 'Stok Obat & Alat Kesehatan')

@section('content')
<div class="card">
    <div class="card-header" style="justify-content: space-between; display: flex; align-items: center;">
        <h3 class="card-title">Data Stok Real-time (Database SIK)</h3>
        
        <form action="{{ url('/farmasi/stok') }}" method="GET" style="display: flex; gap: 8px;">
            <div class="input-group" style="width: 300px;">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari Nama Obat/Alkes..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary btn-sm">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </button>
            </div>
            @if(request('search'))
                <a href="{{ url('/farmasi/stok') }}" class="btn btn-ghost btn-sm" title="Reset Search">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </a>
            @endif
        </form>
    </div>

    <div class="table-wrapper">
        <table class="table-hover">
            <thead>
                <tr>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Satuan</th>
                    <th>Lokasi (Gudang/Unit)</th>
                    <th class="text-center">Stok</th>
                    <th>Harga Beli</th>
                    <th>Harga Jual</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $item)
                <tr>
                    <td><span class="fw-semibold text-primary">{{ $item->kode_brng }}</span></td>
                    <td>{{ $item->nama_brng }}</td>
                    <td><span class="badge badge-gray">{{ $item->satuan }}</span></td>
                    <td><span class="text-muted"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:4px;"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>{{ $item->nm_bangsal }}</span></td>
                    <td class="text-center">
                        @if($item->stok <= 10)
                            <span class="badge badge-orange" style="font-weight: 800; font-size: 13px;">{{ number_format($item->stok, 0) }}</span>
                        @elseif($item->stok <= 0)
                            <span class="badge badge-red" style="font-weight: 800; font-size: 13px;">{{ number_format($item->stok, 0) }}</span>
                        @else
                            <span class="badge badge-green" style="font-weight: 800; font-size: 13px;">{{ number_format($item->stok, 0) }}</span>
                        @endif
                    </td>
                    <td>Rp {{ number_format($item->h_beli, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($item->h_jual, 0, ',', '.') }}</td>
                    <td class="text-center">
                        @if($item->stok > 10)
                            <span class="badge badge-green"><span class="badge-dot"></span> Aman</span>
                        @elseif($item->stok > 0)
                            <span class="badge badge-orange"><span class="badge-dot"></span> Menipis</span>
                        @else
                            <span class="badge badge-red"><span class="badge-dot"></span> Kosong</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-20 text-muted">Data stok tidak ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($data->hasPages())
    <div class="card-footer" style="display: flex; justify-content: center; padding: 20px;">
        {{ $data->links() }}
    </div>
    @endif
</div>

<style>
    /* Custom styles for pagination consistency with the design system */
    .pagination {
        display: flex;
        list-style: none;
        padding: 0;
        gap: 5px;
    }
    .page-item .page-link {
        padding: 6px 12px;
        border: 1.5px solid var(--border);
        border-radius: var(--radius-sm);
        color: var(--text-main);
        text-decoration: none;
        font-size: 13px;
        transition: all 0.2s;
    }
    .page-item.active .page-link {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }
    .page-item.disabled .page-link {
        opacity: 0.5;
        cursor: not-allowed;
    }
    .page-item:not(.active):not(.disabled) .page-link:hover {
        background: var(--bg);
        border-color: var(--primary);
    }
</style>
@endsection
