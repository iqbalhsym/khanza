@extends('layout.app')

@section('title', 'E-Resep')
@section('page-title', 'Resep Obat')
@section('breadcrumb', 'Input Resep')

@section('content')

@if(session('error'))
<div style="margin-bottom:16px;padding:14px 18px;background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.4);border-radius:10px;color:#b91c1c;">
  {{ session('error') }}
</div>
@endif

<div class="page-header">
  <div class="page-header-left">
    <h1>Input E-Resep</h1>
    <p>Resep untuk: <strong class="text-primary">{{ $reg->nm_pasien }}</strong> ({{ $reg->no_rawat }})</p>
  </div>
  @php $backUrl = request('ref') === 'index' ? url('/rawat-jalan') : url('/rawat-jalan/registered/'.urlencode($reg->no_rawat)); @endphp
  <a href="{{ $backUrl }}" class="btn btn-ghost">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    Kembali
  </a>
</div>

<div class="card">
    <div class="card-header">
        <h3>Daftar Obat & Aturan Pakai</h3>
    </div>
    <div class="card-body">
        <form action="{{ url('/rawat-jalan/resep/store') }}" method="POST">
            @csrf
            <input type="hidden" name="no_rawat" value="{{ $reg->no_rawat }}">
            
            <table class="table" id="obat-table">
                <thead>
                    <tr>
                        <th style="width: 40%;">Nama Obat</th>
                        <th style="width: 15%;">Jumlah</th>
                        <th style="width: 35%;">Aturan Pakai</th>
                        <th style="width: 10%;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="obat-tbody">
                    <tr class="obat-row">
                        <td>
                            <select class="form-control" name="obat[]" required>
                                <option value="">-- Pilih Obat --</option>
                                @foreach($obat as $o)
                                    <option value="{{ $o->kode_brng }}">{{ $o->nama_brng }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="number" class="form-control" name="jumlah[]" required min="1" step="0.5" value="1">
                        </td>
                        <td>
                            <input type="text" class="form-control" name="aturan[]" required placeholder="3 x 1 Sesudah Makan">
                        </td>
                        <td>
                            <button type="button" class="btn btn-ghost btn-sm text-danger remove-row">Hapus</button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div style="margin-top: 15px; display: flex; justify-content: space-between; align-items: center;">
                <button type="button" class="btn btn-ghost btn-sm" id="add-row">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Tambah Obat Lain
                </button>
                <button type="submit" class="btn btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:8px;"><polyline points="22 2 15 22 11 13 2 9 22 2"/><line x1="22" y1="2" x2="11" y2="13"/></svg>
                    Kirim Resep ke Farmasi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('add-row').addEventListener('click', function() {
    const tbody = document.getElementById('obat-tbody');
    const firstRow = document.querySelector('.obat-row');
    const newRow = firstRow.cloneNode(true);
    
    // Clear inputs in new row
    newRow.querySelector('select').value = '';
    newRow.querySelector('input[type="number"]').value = '1';
    newRow.querySelector('input[type="text"]').value = '';
    
    tbody.appendChild(newRow);
    
    // Re-attach remove event
    attachRemoveEvent();
});

function attachRemoveEvent() {
    document.querySelectorAll('.remove-row').forEach(btn => {
        btn.onclick = function() {
            const rows = document.querySelectorAll('.obat-row');
            if (rows.length > 1) {
                this.closest('tr').remove();
            } else {
                alert('Minimal harus ada 1 obat dalam resep.');
            }
        };
    });
}

attachRemoveEvent();
</script>

@endsection
