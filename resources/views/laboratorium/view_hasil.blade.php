@extends('layout.app')

@section('title', 'Hasil Laboratorium')
@section('page-title', 'Laboratorium – Detail Hasil')

@section('content')
<div class="card">
    <div class="card-header" style="justify-content: space-between; display: flex; align-items: center;">
        <h3 class="card-title">Laporan Hasil Pemeriksaan Laboratorium</h3>
        <div class="btn-group">
            <button onclick="window.print()" class="btn btn-ghost btn-sm">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                Cetak Hasil
            </button>
            <a href="{{ url('/laboratorium?tab=hasil') }}" class="btn btn-primary btn-sm">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                Kembali
            </a>
        </div>
    </div>

    <div class="card-body" style="padding: 24px;">
        <!-- Header Info -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-bottom: 30px; border-bottom: 2px solid #f1f5f9; padding-bottom: 20px;">
            <div class="info-group">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="width: 140px; color: #64748b; padding: 4px 0;">No. Rawat</td>
                        <td style="font-weight: 600;">: {{ $patientInfo->no_rawat }}</td>
                    </tr>
                    <tr>
                        <td style="color: #64748b; padding: 4px 0;">No. RM</td>
                        <td style="font-weight: 600;">: {{ $patientInfo->no_rkm_medis }}</td>
                    </tr>
                    <tr>
                        <td style="color: #64748b; padding: 4px 0;">Nama Pasien</td>
                        <td style="font-weight: 700; color: #1e293b; font-size: 1.1em;">: {{ $patientInfo->nm_pasien }}</td>
                    </tr>
                    <tr>
                        <td style="color: #64748b; padding: 4px 0;">JK / Tgl Lahir</td>
                        <td>: {{ $patientInfo->jk == 'L' ? 'Laki-laki' : 'Perempuan' }} / {{ $patientInfo->tgl_lahir }}</td>
                    </tr>
                </table>
            </div>
            <div class="info-group">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="width: 140px; color: #64748b; padding: 4px 0;">Tanggal Periksa</td>
                        <td style="font-weight: 600;">: {{ $patientInfo->tgl_periksa }}</td>
                    </tr>
                    <tr>
                        <td style="color: #64748b; padding: 4px 0;">Jam Pemeriksaan</td>
                        <td style="font-weight: 600;">: {{ $patientInfo->jam }}</td>
                    </tr>
                    <tr>
                        <td style="color: #64748b; padding: 4px 0;">Dokter Pemeriksa</td>
                        <td>: {{ $patientInfo->nm_dokter }}</td>
                    </tr>
                    <tr>
                        <td style="color: #64748b; padding: 4px 0;">Kategori</td>
                        <td>: <span class="badge badge-blue">Patologi Klinik (PK)</span></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Result Table -->
        <div class="table-wrapper">
            <table class="table-bordered" style="width: 100%;">
                <thead style="background: #f8fafc;">
                    <tr>
                        <th style="padding: 12px;">Pemeriksaan</th>
                        <th style="padding: 12px; text-align: center;">Hasil</th>
                        <th style="padding: 12px; text-align: center;">Satuan</th>
                        <th style="padding: 12px; text-align: center;">Nilai Rujukan</th>
                        <th style="padding: 12px;">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @php 
                        $currentCategory = ''; 
                    @endphp
                    @foreach($results as $res)
                        @if($currentCategory !== $res->nm_perawatan)
                            <tr style="background: #f1f5f9;">
                                <td colspan="5" style="font-weight: 700; padding: 10px 12px; color: #334155;">{{ $res->nm_perawatan }}</td>
                            </tr>
                            @php $currentCategory = $res->nm_perawatan; @endphp
                        @endif
                        <tr>
                            <td style="padding: 10px 12px; padding-left: 24px;">{{ $res->Pemeriksaan }}</td>
                            <td style="padding: 10px 12px; text-align: center; font-weight: 700;">{{ $res->hasil }}</td>
                            <td style="padding: 10px 12px; text-align: center; color: #64748b;">{{ $res->satuan }}</td>
                            <td style="padding: 10px 12px; text-align: center; font-size: 0.9em; color: #64748b;">
                                {{ $patientInfo->jk == 'L' ? $res->nilai_rujukan_la : $res->nilai_rujukan_ld }}
                            </td>
                            <td style="padding: 10px 12px; color: #64748b;">{{ $res->keterangan }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Footer / Signature -->
        <div style="margin-top: 50px; display: flex; justify-content: flex-end;">
            <div style="text-align: center; width: 250px;">
                <p style="margin-bottom: 60px;">Penanggung Jawab,</p>
                <p style="font-weight: 700; text-decoration: underline;">{{ $patientInfo->nm_dokter }}</p>
                <p style="font-size: 0.8em; color: #64748b;">Dokter Patologi Klinik</p>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        header, .sidebar, .btn-group, .tabs { display: none !important; }
        .card { border: none !important; box-shadow: none !important; }
        body { background: white !important; }
        #app-sidebar, #app-header { display: none !important; }
        main { padding: 0 !important; margin: 0 !important; }
    }
    .table-bordered { border-collapse: collapse; border: 1px solid #e2e8f0; }
    .table-bordered td, .table-bordered th { border: 1px solid #e2e8f0; }
</style>
@endsection
