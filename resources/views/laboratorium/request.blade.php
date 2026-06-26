@extends('layout.app')

@section('title', 'Pemeriksaan Laboratorium Pasien')
@section('page-title', 'Laboratorium – Riwayat & Permintaan')

@section('content')
<div class="row g-3">
    <!-- Data Pasien -->
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Data Pasien</h3>
                <a href="{{ url('/rawat-jalan/registered/' . urlencode($reg->no_rawat)) }}" class="btn btn-sm btn-outline-secondary">
                    ← Kembali ke Detail
                </a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table-info-custom">
                            <tr><th>Nama Pasien</th><td>: <strong>{{ $reg->nm_pasien }}</strong></td></tr>
                            <tr><th>No. Rawat</th><td>: {{ $reg->no_rawat }}</td></tr>
                            <tr><th>No. RM</th><td>: {{ $reg->no_rkm_medis }}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table-info-custom">
                            <tr><th>Poliklinik</th><td>: {{ $reg->nm_poli ?? '-' }}</td></tr>
                            <tr><th>Dokter Perujuk</th><td>: {{ $reg->nm_dokter ?? '-' }}</td></tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Hasil Selesai -->
    <div class="col-md-6">
        <div class="card" style="min-height: 350px;">
            <div class="card-header bg-teal-lt">
                <h3 class="card-title text-teal">✅ Hasil Pemeriksaan Selesai</h3>
                <span class="badge bg-teal text-teal-light ms-2">{{ $lab_results->count() }}</span>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-nowrap">
                    <thead>
                        <tr>
                            <th>Tgl. Periksa</th>
                            <th>Pemeriksaan</th>
                            <th>Pemeriksa</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lab_results as $lab)
                        <tr>
                            <td class="small text-muted">{{ $lab->tgl_periksa }} <div class="small">{{ $lab->jam }}</div></td>
                            <td class="fw-semibold">{{ $lab->item_name }}</td>
                            <td class="text-muted small">{{ $lab->examiner }}</td>
                            <td>
                                <a href="{{ url('/laboratorium/view-hasil/'.urlencode($lab->no_rawat).'/'.urlencode($lab->tgl_periksa).'/'.urlencode($lab->jam)) }}" 
                                   class="btn btn-xs btn-teal">
                                    Lihat Hasil
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-5 italic" style="font-size:12px;">
                                Belum ada hasil pemeriksaan selesai.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Daftar Antrian Permintaan Pending -->
    <div class="col-md-6">
        <div class="card" style="min-height: 350px;">
            <div class="card-header bg-warning-lt">
                <h3 class="card-title text-warning">⏳ Antrian Permintaan Rujukan</h3>
                <span class="badge bg-warning text-warning-light ms-2">{{ $lab_pending->count() }}</span>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>No. Order</th>
                            <th>Tgl. Permintaan</th>
                            <th>Tindakan / Pemeriksaan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lab_pending as $pending)
                        <tr>
                            <td class="fw-bold text-primary small">{{ $pending->noorder }}</td>
                            <td class="small text-muted">
                                {{ $pending->tgl_permintaan }}
                                <div class="small">{{ $pending->jam_permintaan }}</div>
                            </td>
                            <td>
                                <div style="font-size: 12px; font-weight: 500;">{{ $pending->test_names ?: '-' }}</div>
                                <div class="text-muted small" style="font-size:10px;">Perujuk: {{ $pending->dokter_perujuk_nama }}</div>
                            </td>
                            <td>
                                <span class="badge bg-warning-lt">Menunggu</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-5 italic" style="font-size:12px;">
                                Tidak ada antrian permintaan aktif.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .table-info-custom th { text-align: left; padding: 6px 12px 6px 0; color: #64748b; font-weight: 500; width: 130px; font-size: 13px; }
    .table-info-custom td { padding: 6px 0; font-size: 13px; }
    .btn-xs { padding: 2px 6px; font-size: 11px; border-radius: 4px; }
</style>
@endsection
