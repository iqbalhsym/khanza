<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FarmasiController extends Controller
{
    /**
     * Display medical stock from SIK database.
     */
    public function stok(Request $request)
    {
        $query = DB::table('gudangbarang')
            ->join('databarang', 'gudangbarang.kode_brng', '=', 'databarang.kode_brng')
            ->join('bangsal', 'gudangbarang.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->join('kodesatuan', 'databarang.kode_sat', '=', 'kodesatuan.kode_sat')
            ->select(
                'databarang.kode_brng',
                'databarang.nama_brng',
                'kodesatuan.satuan',
                'bangsal.nm_bangsal',
                'gudangbarang.stok',
                'databarang.h_beli',
                'databarang.ralan as h_jual'
            )
            ->where('databarang.status', '1');

        // Handle search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('databarang.nama_brng', 'like', "%{$search}%")
                  ->orWhere('databarang.kode_brng', 'like', "%{$search}%")
                  ->orWhere('bangsal.nm_bangsal', 'like', "%{$search}%");
            });
        }

        $data = $query->orderBy('databarang.nama_brng', 'asc')
            ->paginate(25)
            ->withQueryString();

        return view('farmasi.stok', compact('data'));
    }
}
