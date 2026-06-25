<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MasterController extends Controller
{
    public function pasien(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 20);
        
        $query = DB::table('pasien')
            ->select('pasien.*')
            ->orderBy('nm_pasien', 'asc');
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('pasien.no_rkm_medis', 'like', '%' . $search . '%')
                  ->orWhere('pasien.nm_pasien', 'like', '%' . $search . '%')
                  ->orWhere('pasien.no_ktp', 'like', '%' . $search . '%')
                  ->orWhere('pasien.alamat', 'like', '%' . $search . '%');
            });
        }
        
        $data = $query->paginate($perPage);
        
        // Tambahkan parameter pencarian ke pagination links
        if ($search) {
            $data->appends(['search' => $search]);
        }
        if ($perPage != 20) {
            $data->appends(['per_page' => $perPage]);
        }
        
        return view('master.pasien', compact('data', 'search', 'perPage'));
    }

    public function dokter(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 20);
        $statusFilter = $request->input('status', 'all'); // all, active, inactive
        
        // Ambil statistik jumlah dokter dari database dokter
        $stats = DB::connection('dokter')->table('dokter')
            ->selectRaw("
                count(*) as total,
                sum(case when status = '1' then 1 else 0 end) as aktif,
                sum(case when status = '1' then 0 else 1 end) as tidak_aktif
            ")
            ->first();

        $sikDb = config('database.connections.mysql.database', 'sik');

        $query = DB::connection('dokter')->table('dokter')
            ->leftJoin("{$sikDb}.spesialis as spesialis", 'dokter.kd_sps', '=', 'spesialis.kd_sps')
            ->select('dokter.*', 'spesialis.nm_sps');
        
        // Terapkan filter status
        if ($statusFilter === 'active') {
            $query->where('dokter.status', '1');
        } elseif ($statusFilter === 'inactive') {
            $query->where(function($q) {
                $q->where('dokter.status', '!=', '1')->orWhereNull('dokter.status');
            });
        }
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('dokter.kd_dokter', 'like', '%' . $search . '%')
                  ->orWhere('dokter.nm_dokter', 'like', '%' . $search . '%')
                  ->orWhere('spesialis.nm_sps', 'like', '%' . $search . '%');
            });
        }
        
        $data = $query->orderBy('dokter.nm_dokter', 'asc')->paginate($perPage);
        
        // Tambahkan parameter ke link pagination
        $data->appends([
            'search' => $search,
            'per_page' => $perPage,
            'status' => $statusFilter
        ]);
        
        return view('master.dokter', compact('data', 'search', 'perPage', 'statusFilter', 'stats'));
    }

    public function toggleStatus($kd_dokter)
    {
        $dokter = DB::connection('dokter')->table('dokter')->where('kd_dokter', $kd_dokter)->first();
        if ($dokter) {
            $newStatus = $dokter->status == '1' ? '0' : '1';
            DB::connection('dokter')->table('dokter')->where('kd_dokter', $kd_dokter)->update(['status' => $newStatus]);
            
            $statusLabel = $newStatus == '1' ? 'aktif' : 'tidak aktif';
            return redirect()->back()->with('success', "Status dr. {$dokter->nm_dokter} berhasil diubah menjadi {$statusLabel}.");
        }
        return redirect()->back()->with('error', 'Dokter tidak ditemukan.');
    }

    public function poli(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 20);
        
        $query = DB::table('poliklinik')
            ->where('status', '1');
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('kd_poli', 'like', '%' . $search . '%')
                  ->orWhere('nm_poli', 'like', '%' . $search . '%');
            });
        }
        
        $data = $query->orderBy('nm_poli', 'asc')->paginate($perPage);
        
        // Tambahkan parameter pencarian ke pagination links
        if ($search) {
            $data->appends(['search' => $search]);
        }
        if ($perPage != 20) {
            $data->appends(['per_page' => $perPage]);
        }
        
        return view('master.poli', compact('data', 'search', 'perPage'));
    }

    public function obat(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 20);
        
        $query = DB::table('databarang')
            ->join('kodesatuan', 'databarang.kode_sat', '=', 'kodesatuan.kode_sat')
            ->select('databarang.*', 'kodesatuan.satuan')
            ->where('status', '1');
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('databarang.kode_brng', 'like', '%' . $search . '%')
                  ->orWhere('databarang.nama_brng', 'like', '%' . $search . '%')
                  ->orWhere('kodesatuan.satuan', 'like', '%' . $search . '%');
            });
        }
        
        $data = $query->orderBy('nama_brng', 'asc')->paginate($perPage);
        
        // Tambahkan parameter pencarian ke pagination links
        if ($search) {
            $data->appends(['search' => $search]);
        }
        if ($perPage != 20) {
            $data->appends(['per_page' => $perPage]);
        }
        
        return view('master.obat', compact('data', 'search', 'perPage'));
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

    public function tarif(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 20);
        
        $query = DB::table('jns_perawatan')
            ->leftJoin('poliklinik', 'jns_perawatan.kd_poli', '=', 'poliklinik.kd_poli')
            ->leftJoin('penjab', 'jns_perawatan.kd_pj', '=', 'penjab.kd_pj')
            ->select('jns_perawatan.*', 'poliklinik.nm_poli', 'penjab.png_jawab')
            ->where('jns_perawatan.status', '1');
            
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('jns_perawatan.kd_jenis_prw', 'like', '%' . $search . '%')
                  ->orWhere('jns_perawatan.nm_perawatan', 'like', '%' . $search . '%')
                  ->orWhere('poliklinik.nm_poli', 'like', '%' . $search . '%')
                  ->orWhere('penjab.png_jawab', 'like', '%' . $search . '%');
            });
        }
        
        $data = $query->orderBy('jns_perawatan.nm_perawatan', 'asc')->paginate($perPage);
        
        if ($search) {
            $data->appends(['search' => $search]);
        }
        if ($perPage != 20) {
            $data->appends(['per_page' => $perPage]);
        }
        
        return view('master.tarif', compact('data', 'search', 'perPage'));
    }

    public function aset(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 20);
        
        $query = DB::table('inventaris_barang')
            ->leftJoin('inventaris_jenis', 'inventaris_barang.id_jenis', '=', 'inventaris_jenis.id_jenis')
            ->leftJoin('inventaris_merk', 'inventaris_barang.id_merk', '=', 'inventaris_merk.id_merk')
            ->leftJoin('inventaris_produsen', 'inventaris_barang.kode_produsen', '=', 'inventaris_produsen.kode_produsen')
            ->leftJoin('inventaris_kategori', 'inventaris_barang.id_kategori', '=', 'inventaris_kategori.id_kategori')
            ->select(
                'inventaris_barang.*',
                'inventaris_jenis.nama_jenis',
                'inventaris_merk.nama_merk',
                'inventaris_produsen.nama_produsen',
                'inventaris_kategori.nama_kategori'
            );
            
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('inventaris_barang.kode_barang', 'like', '%' . $search . '%')
                  ->orWhere('inventaris_barang.nama_barang', 'like', '%' . $search . '%')
                  ->orWhere('inventaris_jenis.nama_jenis', 'like', '%' . $search . '%')
                  ->orWhere('inventaris_merk.nama_merk', 'like', '%' . $search . '%');
            });
        }
        
        $data = $query->orderBy('inventaris_barang.nama_barang', 'asc')->paginate($perPage);
        
        if ($search) {
            $data->appends(['search' => $search]);
        }
        if ($perPage != 20) {
            $data->appends(['per_page' => $perPage]);
        }
        
        return view('master.aset', compact('data', 'search', 'perPage'));
    }
}
