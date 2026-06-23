<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Session;

class ResepController extends Controller
{
    public function index()
    {
        // Pharmacy Dashboard - antrian resep hari ini yang belum diserahkan
        $today = date('Y-m-d');
        
        $resep = DB::table('resep_obat')
            ->join('reg_periksa', 'resep_obat.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('dokter', 'resep_obat.kd_dokter', '=', 'dokter.kd_dokter')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->where('resep_obat.tgl_peresepan', $today)
            ->select(
                'resep_obat.*', 
                'reg_periksa.no_rkm_medis', 
                'pasien.nm_pasien', 
                'dokter.nm_dokter', 
                'poliklinik.nm_poli'
            )
            ->orderBy('resep_obat.jam_peresepan', 'desc')
            ->get();

        foreach ($resep as $r) {
            $r->items = DB::table('resep_dokter')
                ->join('databarang', 'resep_dokter.kode_brng', '=', 'databarang.kode_brng')
                ->where('no_resep', $r->no_resep)
                ->select('databarang.nama_brng', 'resep_dokter.jml', 'resep_dokter.aturan_pakai')
                ->get();
        }

        return view('farmasi.index', compact('resep'));
    }

    public function create($no_rawat)
    {
        $no_rawat = urldecode($no_rawat);
        $reg = DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->where('no_rawat', $no_rawat)
            ->select('reg_periksa.*', 'pasien.nm_pasien')
            ->first();

        if (!$reg) return redirect()->back()->with('error', 'Data registrasi tidak ditemukan.');

        // Get 100 medications for selection
        $obat = DB::table('databarang')
            ->where('status', '1')
            ->select('kode_brng', 'nama_brng', 'ralan')
            ->orderBy('nama_brng', 'asc')
            ->limit(100)
            ->get();

        return view('rawat_jalan.resep', compact('reg', 'obat'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_rawat' => 'required',
            'obat' => 'required|array|min:1',
            'jumlah' => 'required|array',
            'aturan' => 'required|array',
        ]);

        try {
            DB::beginTransaction();

            // Generate no_resep (YYYYMMDDXXXXXX)
            $prefix = date('Ymd');
            $last = DB::table('resep_obat')
                ->where('no_resep', 'like', $prefix . '%')
                ->orderBy('no_resep', 'desc')
                ->first();
            
            if ($last) {
                $last_seq = (int) substr($last->no_resep, -4);
                $next_seq = str_pad($last_seq + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $next_seq = '0001';
            }
            $no_resep = $prefix . $next_seq;

            $reg = DB::table('reg_periksa')->where('no_rawat', $request->no_rawat)->first();

            // Insert Header
            DB::table('resep_obat')->insert([
                'no_resep'      => $no_resep,
                'tgl_perawatan' => date('Y-m-d'),
                'jam'           => date('H:i:s'),
                'no_rawat'      => $request->no_rawat,
                'kd_dokter'     => $reg->kd_dokter,
                'tgl_peresepan' => date('Y-m-d'),
                'jam_peresepan' => date('H:i:s'),
                'status'        => 'ralan',
                'tgl_penyerahan' => '1000-01-01', // Use 1000-01-01 for NO_ZERO_DATE strict mode
                'jam_penyerahan' => '00:00:00'
            ]);

            // Insert Details
            foreach ($request->obat as $key => $kode_brng) {
                if (!empty($kode_brng)) {
                    DB::table('resep_dokter')->insert([
                        'no_resep'     => $no_resep,
                        'kode_brng'    => $kode_brng,
                        'jml'          => $request->jumlah[$key],
                        'aturan_pakai' => $request->aturan[$key]
                    ]);
                }
            }

            DB::commit();
            return redirect('/rawat-jalan')->with('success', 'E-Resep berhasil dikirim ke Farmasi. No: ' . $no_resep);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal membuat resep: ' . $e->getMessage());
        }
    }

    public function searchObat(Request $request)
    {
        $q = trim($request->query('q', ''));

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $results = DB::table('databarang')
            ->where('status', '1')
            ->where(function ($query) use ($q) {
                $query->whereRaw('LOWER(nama_brng) LIKE ?', ['%' . strtolower($q) . '%'])
                      ->orWhereRaw('LOWER(kode_brng) LIKE ?', ['%' . strtolower($q) . '%']);
            })
            ->select('kode_brng', 'nama_brng', 'kode_sat')
            ->orderBy('nama_brng', 'asc')
            ->limit(25)
            ->get();

        return response()->json($results);
    }

    public function dispense($no_resep)
    {
        try {
            DB::table('resep_obat')->where('no_resep', $no_resep)->update([
                'tgl_penyerahan' => date('Y-m-d'),
                'jam_penyerahan' => date('H:i:s')
            ]);
            return redirect()->back()->with('success', 'Obat telah diserahkan ke pasien.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal update penyerahan: ' . $e->getMessage());
        }
    }
}
