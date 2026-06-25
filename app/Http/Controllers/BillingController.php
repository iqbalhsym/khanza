<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Session;

class BillingController extends Controller
{
    public function index()
    {
        // Antrian Kasir - Pasien Sudah Diperiksa tapi Belum Bayar
        $today = date('Y-m-d');
        
        $antrian = DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->where('reg_periksa.tgl_registrasi', $today)
            ->where('reg_periksa.status_bayar', 'Belum Bayar')
            ->where('reg_periksa.stts', 'Sudah') // Berhasil diperiksa Dokter
            ->select(
                'reg_periksa.*', 'pasien.nm_pasien', 'poliklinik.nm_poli'
            )
            ->orderBy('reg_periksa.jam_reg', 'desc')
            ->get();

        // Map doctor names in memory
        $kd_dokters = $antrian->pluck('kd_dokter')->unique()->filter()->toArray();
        if (!empty($kd_dokters)) {
            $dokters = DB::connection('dokter')->table('dokter')
                ->whereIn('kd_dokter', $kd_dokters)
                ->pluck('nm_dokter', 'kd_dokter');
        } else {
            $dokters = collect();
        }

        foreach ($antrian as $item) {
            $item->nm_dokter = $dokters[$item->kd_dokter] ?? '-';
        }

        return view('billing.index', compact('antrian'));
    }

    public function show($no_rawat)
    {
        $no_rawat = urldecode($no_rawat);
        
        $reg = DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->where('reg_periksa.no_rawat', $no_rawat)
            ->select('reg_periksa.*', 'pasien.nm_pasien', 'pasien.alamat', 'poliklinik.nm_poli', 'penjab.png_jawab')
            ->first();

        if (!$reg) return redirect()->back()->with('error', 'Data registrasi tidak ditemukan.');

        // Get doctor name from connection 'dokter'
        if (!empty($reg->kd_dokter)) {
            $doc = DB::connection('dokter')->table('dokter')->where('kd_dokter', $reg->kd_dokter)->first();
            $reg->nm_dokter = $doc->nm_dokter ?? '-';
        } else {
            $reg->nm_dokter = '-';
        }

        // 1. Biaya Registrasi
        $biaya_reg = $reg->biaya_reg;

        // 2. Biaya Obat (dari E-Resep yang sudah dibuat)
        $obat = DB::table('resep_obat')
            ->join('resep_dokter', 'resep_obat.no_resep', '=', 'resep_dokter.no_resep')
            ->join('databarang', 'resep_dokter.kode_brng', '=', 'databarang.kode_brng')
            ->where('resep_obat.no_rawat', $no_rawat)
            ->select('databarang.nama_brng', 'resep_dokter.jml', 'databarang.ralan as harga')
            ->get();
        
        $total_obat = $obat->sum(fn($o) => $o->jml * $o->harga);

        $total_bayar = $biaya_reg + $total_obat;

        return view('billing.show', compact('reg', 'biaya_reg', 'obat', 'total_obat', 'total_bayar'));
    }

    public function pay(Request $request)
    {
        $request->validate(['no_rawat' => 'required']);

        try {
            DB::beginTransaction();
            $no_rawat = $request->no_rawat;

            // Generate No Nota (YYYY/MM/DD/RJXXXX)
            $prefix = date('Y/m/d/RJ');
            $last = DB::table('nota_jalan')
                ->where('no_nota', 'like', $prefix . '%')
                ->orderBy('no_nota', 'desc')
                ->first();
            
            if ($last) {
                $last_seq = (int) substr($last->no_nota, -4);
                $next_seq = str_pad($last_seq + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $next_seq = '0001';
            }
            $no_nota = $prefix . $next_seq;

            // 1. Insert ke nota_jalan
            DB::table('nota_jalan')->insert([
                'no_rawat' => $no_rawat,
                'no_nota'  => $no_nota,
                'tanggal'  => date('Y-m-d'),
                'jam'      => date('H:i:s')
            ]);

            // 2. Insert detail rincian (Registrasi)
            DB::table('detail_nota_jalan')->insert([
                'no_rawat'    => $no_rawat,
                'nama_bayar'  => 'Registrasi & Pemeriksaan',
                'besar_bayar' => $request->biaya_reg,
                'besarppn'    => 0
            ]);

            // 3. Insert detail rincian (Obat)
            if ($request->total_obat > 0) {
                DB::table('detail_nota_jalan')->insert([
                    'no_rawat'    => $no_rawat,
                    'nama_bayar'  => 'Obat & Alkes',
                    'besar_bayar' => $request->total_obat,
                    'besarppn'    => 0
                ]);
            }

            // 4. Update status bayar di reg_periksa
            DB::table('reg_periksa')->where('no_rawat', $no_rawat)->update([
                'status_bayar' => 'Sudah Bayar'
            ]);

            DB::commit();
            return redirect('/billing')->with('success', 'Pembayaran berhasil diproses. No. Nota: ' . $no_nota);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }
}
