@extends('layout.app')

@section('title', 'Proses Pembayaran')
@section('page-title', 'Billing Pasien')
@section('breadcrumb', 'Detail Tagihan')

@section('content')

@if(session('error'))
<div style="margin-bottom:16px;padding:14px 18px;background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.4);border-radius:10px;color:#b91c1c;">
  {{ session('error') }}
</div>
@endif

<div class="page-header">
  <div class="page-header-left">
    <h1>Review Billing Pasien</h1>
    <p>No. Rawat: <strong class="text-primary">{{ $reg->no_rawat }}</strong></p>
  </div>
  <a href="{{ url('/rawat-jalan/registered/'.urlencode($reg->no_rawat)) }}" class="btn btn-ghost">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    Kembali
  </a>
</div>

<div style="display:grid; grid-template-columns: 1fr 340px; gap: 20px;">
    
    <!-- Rincian Kiri -->
    <div>
        <div class="card mb-16">
            <div class="card-header"><h3>Informasi Pasien</h3></div>
            <div class="card-body" style="display:grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <div style="font-size:12px; color:var(--text-muted)">Nama Pasien</div>
                    <div style="font-weight:700; font-size:16px;">{{ $reg->nm_pasien }}</div>
                    <div style="font-size:13px;">No. RM: {{ $reg->no_rkm_medis }}</div>
                </div>
                <div>
                    <div style="font-size:12px; color:var(--text-muted)">Dokter / Poliklinik</div>
                    <div style="font-weight:600;">{{ $reg->nm_dokter }}</div>
                    <div style="font-size:13px;">{{ $reg->nm_poli }}</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3>Rincian Biaya Layanan</h3></div>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Deskripsi Layanan / Item</th>
                            <th style="text-align:right">Harga/Biaya</th>
                            <th style="text-align:right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Registrasi & Pemeriksaan Umum</td>
                            <td style="text-align:right">Rp {{ number_format($biaya_reg, 0, ',', '.') }}</td>
                            <td style="text-align:right">Rp {{ number_format($biaya_reg, 0, ',', '.') }}</td>
                        </tr>
                        @foreach($obat as $o)
                        <tr>
                            <td>{{ $o->nama_brng }} (Qty: {{ $o->jml }})</td>
                            <td style="text-align:right">Rp {{ number_format($o->harga, 0, ',', '.') }}</td>
                            <td style="text-align:right">Rp {{ number_format($o->jml * $o->harga, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="background:var(--bg-lighter); font-weight:700;">
                            <td colspan="2" style="text-align:right">TOTAL TAGIHAN</td>
                            <td style="text-align:right; color:var(--primary); font-size:18px;">Rp {{ number_format($total_bayar, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Panel Kanan (Pembayaran) -->
    <div>
        <div class="card" style="position: sticky; top: 20px;">
            <div class="card-header"><h3>Konfirmasi</h3></div>
            <div class="card-body">
                <div style="margin-bottom:20px;">
                    <div style="font-size:12px; color:var(--text-muted); margin-bottom:5px;">Cara Bayar</div>
                    <div style="padding:10px; background:var(--bg-lighter); border-radius:8px; font-weight:600; text-align:center;">
                        {{ $reg->png_jawab }} ({{ $reg->kd_pj }})
                    </div>
                </div>

                <form action="{{ url('/billing/pay') }}" method="POST">
                    @csrf
                    <input type="hidden" name="no_rawat" value="{{ $reg->no_rawat }}">
                    <input type="hidden" name="biaya_reg" value="{{ $biaya_reg }}">
                    <input type="hidden" name="total_obat" value="{{ $total_obat }}">

                    <div style="margin-bottom:20px;">
                        <label class="form-label">Metode Pembayaran</label>
                        <select class="form-control" name="metode" required>
                            <option value="Tunai">Tunai / Cash</option>
                            <option value="Transfer">Transfer Bank</option>
                            <option value="Debit">Kartu Debit</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block" style="width:100%; padding:14px; font-size:16px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:8px;"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                        Selesaikan Pembayaran
                    </button>
                </form>

                <div style="margin-top:15px; font-size:12px; color:var(--text-muted); text-align:center;">
                    Pastikan rincian biaya sudah benar sebelum menekan tombol bayar.
                </div>
            </div>
        </div>
    </div>

</div>

@endsection
