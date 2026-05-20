@extends('layout.app')

@section('title', 'Peta Kamar')
@section('page-title', 'Rawat Inap – Peta Kamar')

@section('content')
<!-- Tabs -->
<div class="tabs mb-20">
    <a href="{{ url('/rawat-inap') }}" class="tab-btn {{ Request::is('rawat-inap') ? 'active' : '' }}">
        Pasien Dirawat
    </a>
    <a href="{{ url('/rawat-inap/kamar') }}" class="tab-btn {{ Request::is('rawat-inap/kamar') ? 'active' : '' }}">
        Peta Kamar
    </a>
</div>

@foreach($wards as $w)
@php
    $wardBeds = $beds->where('kd_bangsal', $w->kd_bangsal);
@endphp
<div class="card mb-16">
    <div class="card-header">
        <h3>Bangsal {{ $w->nm_bangsal }}</h3>
        <div style="display:flex;gap:12px;font-size:11px;align-items:center;">
            <span style="display:flex;align-items:center;gap:5px;"><span style="width:10px;height:10px;background:#34D399;border-radius:2px;display:inline-block;"></span>Kosong</span>
            <span style="display:flex;align-items:center;gap:5px;"><span style="width:10px;height:10px;background:#FCA5A5;border-radius:2px;display:inline-block;"></span>Terisi</span>
            <span style="display:flex;align-items:center;gap:5px;"><span style="width:10px;height:10px;background:#FCD34D;border-radius:2px;display:inline-block;"></span>Booking</span>
        </div>
    </div>
    <div class="card-body">
        <div class="bed-grid">
            @foreach($wardBeds as $bed)
            @php
                $statusClass = strtolower($bed->status);
                $badgeClass = $bed->status === 'KOSONG' ? 'badge-green' : ($bed->status === 'ISI' ? 'badge-red' : 'badge-orange');
                $stateClass = $bed->status === 'KOSONG' ? 'available' : ($bed->status === 'ISI' ? 'occupied' : 'reserved');
            @endphp
            <div class="bed-card {{ $stateClass }}" title="{{ $bed->kd_kamar }} - Status: {{ $bed->status }}">
                <div class="bed-icon">🛏️</div>
                <div class="bed-num">{{ $bed->kd_kamar }}</div>
                <div class="bed-class text-xs" style="margin-top:3px;">
                    <span class="badge {{ $badgeClass }}" style="font-size:10px;padding:2px 6px;">{{ $bed->status }}</span>
                </div>
                <div class="text-xs text-muted" style="margin-top:4px;">{{ $bed->kelas }}</div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endforeach

<style>
    .bed-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 15px; }
    .bed-card { 
        padding: 15px; border-radius: 12px; background: #fff; border: 1px solid #e2e8f0; text-align: center; 
        transition: all 0.2s ease; cursor: default;
    }
    .bed-card:hover { transform: translateY(-3px); box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    .bed-card.available { border-top: 4px solid #10b981; background: rgba(16, 185, 129, 0.02); }
    .bed-card.occupied { border-top: 4px solid #ef4444; background: rgba(239, 68, 68, 0.02); }
    .bed-card.reserved { border-top: 4px solid #f59e0b; background: rgba(245, 158, 11, 0.02); }
    .bed-icon { font-size: 24px; margin-bottom: 8px; }
    .bed-num { font-weight: 700; font-size: 14px; color: var(--text-main); }
</style>
@endsection
