<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class MasterController extends Controller
{
    public function dokter()
    {
        $data = DB::table('dokter')
            ->join('spesialis', 'dokter.kd_sps', '=', 'spesialis.kd_sps')
            ->select('dokter.*', 'spesialis.nm_sps')
            ->where('status', '1')
            ->orderBy('nm_dokter', 'asc')
            ->paginate(15);
        
        return view('master.dokter', compact('data'));
    }

    public function poli()
    {
        $data = DB::table('poliklinik')
            ->where('status', '1')
            ->orderBy('nm_poli', 'asc')
            ->get();
        
        return view('master.poli', compact('data'));
    }

    public function obat()
    {
        $data = DB::table('databarang')
            ->join('kodesatuan', 'databarang.kode_sat', '=', 'kodesatuan.kode_sat')
            ->select('databarang.*', 'kodesatuan.satuan')
            ->where('status', '1')
            ->orderBy('nama_brng', 'asc')
            ->paginate(20);
        
        return view('master.obat', compact('data'));
    }

    public function kamar()
    {
        $data = DB::table('kamar')
            ->join('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->select('kamar.*', 'bangsal.nm_bangsal')
            ->orderBy('kamar.kd_kamar', 'asc')
            ->paginate(20);
        
        return view('master.kamar', compact('data'));
    }
}
