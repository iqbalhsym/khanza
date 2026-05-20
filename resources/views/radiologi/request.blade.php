@extends('layout.app')

@section('title', 'Permintaan Radiologi')
@section('page-title', 'Radiologi – Buat Rujukan')

@section('content')
<div class="card mb-20">
    <div class="card-header">
        <h3 class="card-title">Data Pasien</h3>
    </div>
    <div class="card-body">
        <div style="display:flex; gap:40px;">
            <table class="table-info">
                <tr><th>Nama Pasien</th><td>: <span class="fw-bold">{{ $reg->nm_pasien }}</span></td></tr>
                <tr><th>No. Rawat</th><td>: {{ $reg->no_rawat }}</td></tr>
                <tr><th>No. RM</th><td>: {{ $reg->no_rkm_medis }}</td></tr>
            </table>
            <table class="table-info">
                <tr><th>Poliklinik</th><td>: {{ $reg->nm_poli ?? '-' }}</td></tr>
                <tr><th>Dokter Perujuk</th><td>: {{ $reg->nm_dokter ?? '-' }}</td></tr>
            </table>
        </div>
    </div>
</div>

<form action="{{ url('/radiologi/request/store') }}" method="POST">
    @csrf
    <input type="hidden" name="no_rawat" value="{{ $reg->no_rawat }}">

    <div class="card">
        <div class="card-header" style="justify-content: space-between; display: flex; align-items: center;">
            <h3 class="card-title">Pilih Jenis Tindakan Radiologi</h3>
            <div class="input-group" style="width: 250px;">
                <input type="text" id="pemeriksaanSearch" class="form-control form-control-sm" placeholder="Cari tindakan...">
            </div>
        </div>
        <div class="card-body" style="max-height: 500px; overflow-y: auto; padding: 0;">
            <table class="table-hover table-sm">
                <thead>
                    <tr>
                        <th style="width: 50px;">Pilih</th>
                        <th>Kode</th>
                        <th>Nama Tindakan</th>
                        <th>Biaya</th>
                    </tr>
                </thead>
                <tbody id="pemeriksaanTable">
                    @foreach($jenis_pemeriksaan as $j)
                    <tr class="pemeriksaan-row">
                        <td class="text-center">
                            <input type="checkbox" name="kd_jenis_prw[]" value="{{ $j->kd_jenis_prw }}" style="width:18px; height:18px; cursor:pointer;">
                        </td>
                        <td><span class="text-muted small">{{ $j->kd_jenis_prw }}</span></td>
                        <td class="fw-semibold">{{ $j->nm_perawatan }}</td>
                        <td>Rp {{ number_format($j->total_byr, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer" style="display:flex; justify-content: flex-end; gap: 10px; padding: 20px;">
            @php $backUrl = request('ref') === 'index' ? url('/rawat-jalan') : url('/rawat-jalan/registered/'.urlencode($reg->no_rawat)); @endphp
            <a href="{{ $backUrl }}" class="btn btn-ghost">Batal</a>
            <button type="submit" class="btn btn-primary px-40">Kirim Rujukan Radiologi</button>
        </div>
    </div>
</form>

<style>
    .table-info th { text-align: left; padding: 5px 15px 5px 0; color: var(--text-muted); font-weight: normal; width: 120px; }
    .table-info td { padding: 5px 0; }
    .table-sm th, .table-sm td { padding: 8px 15px; }
</style>

<script>
    document.getElementById('pemeriksaanSearch').addEventListener('keyup', function() {
        let val = this.value.toLowerCase();
        document.querySelectorAll('.pemeriksaan-row').forEach(row => {
            let text = row.innerText.toLowerCase();
            row.style.display = text.includes(val) ? '' : 'none';
        });
    });
</script>
@endsection
