<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RadiologiController extends Controller
{
    /**
     * Display Radiologi Dashboard with Tabs (Queue & Results).
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'antrian');
        
        if ($tab === 'hasil') {
            // Data for "Hasil Pemeriksaan" (Already Examined)
            $query = DB::table('periksa_radiologi')
                ->join('reg_periksa', 'periksa_radiologi.no_rawat', '=', 'reg_periksa.no_rawat')
                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->join('jns_perawatan_radiologi', 'periksa_radiologi.kd_jenis_prw', '=', 'jns_perawatan_radiologi.kd_jenis_prw')
                ->select(
                    'periksa_radiologi.tgl_periksa as tgl_permintaan',
                    'periksa_radiologi.jam as jam_permintaan',
                    'periksa_radiologi.no_rawat',
                    'periksa_radiologi.kd_dokter',
                    'pasien.nm_pasien',
                    'jns_perawatan_radiologi.nm_perawatan',
                    DB::raw("'selesai' as stts_data")
                );
        } else {
            // Data for "Antrian Permintaan" (Pending)
            $query = DB::table('permintaan_radiologi')
                ->join('reg_periksa', 'permintaan_radiologi.no_rawat', '=', 'reg_periksa.no_rawat')
                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->select(
                    'permintaan_radiologi.*',
                    'permintaan_radiologi.dokter_perujuk as kd_dokter',
                    'pasien.nm_pasien',
                    DB::raw("'antrian' as stts_data")
                );
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('pasien.nm_pasien', 'like', "%{$search}%")
                  ->orWhere('reg_periksa.no_rawat', 'like', "%{$search}%");
            });
        }

        $data = $query->orderBy('tgl_permintaan', 'desc')
            ->orderBy('jam_permintaan', 'desc')
            ->paginate(20)
            ->withQueryString();

        // Ambil nama dokter dari database dokter secara terpisah
        $kd_dokters = collect($data->items())->pluck('kd_dokter')->unique()->toArray();
        if (!empty($kd_dokters)) {
            $dokters = DB::connection('dokter')->table('dokter')
                ->whereIn('kd_dokter', $kd_dokters)
                ->pluck('nm_dokter', 'kd_dokter');
        } else {
            $dokters = collect();
        }

        foreach ($data->items() as $item) {
            $item->dokter_pengirim = $dokters[$item->kd_dokter] ?? '-';
        }

        return view('radiologi.index', compact('data', 'tab'));
    }

    /**
     * Display referral form for Radiology.
     */
    public function createRequest($no_rawat)
    {
        $no_rawat = urldecode($no_rawat);
        $reg = DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->where('no_rawat', $no_rawat)
            ->select('reg_periksa.*', 'pasien.nm_pasien', 'poliklinik.nm_poli')
            ->first();

        if (!$reg) return redirect()->back()->with('error', 'Data pendaftaran tidak ditemukan.');

        // Get doctor name from connection 'dokter'
        if (!empty($reg->kd_dokter)) {
            $doc = DB::connection('dokter')->table('dokter')->where('kd_dokter', $reg->kd_dokter)->first();
            $reg->nm_dokter = $doc->nm_dokter ?? '-';
        } else {
            $reg->nm_dokter = '-';
        }

        // Fetch completed radiology results
        $rad_results = DB::table('periksa_radiologi')
            ->join('jns_perawatan_radiologi', 'periksa_radiologi.kd_jenis_prw', '=', 'jns_perawatan_radiologi.kd_jenis_prw')
            ->where('periksa_radiologi.no_rawat', $no_rawat)
            ->select('periksa_radiologi.*', 'jns_perawatan_radiologi.nm_perawatan as item_name', 'periksa_radiologi.kd_dokter')
            ->orderBy('periksa_radiologi.tgl_periksa', 'desc')
            ->orderBy('periksa_radiologi.jam', 'desc')
            ->get();

        $rad_doc_ids = $rad_results->pluck('kd_dokter')->unique()->toArray();
        if (!empty($rad_doc_ids)) {
            $rad_docs = DB::connection('dokter')->table('dokter')
                ->whereIn('kd_dokter', $rad_doc_ids)
                ->pluck('nm_dokter', 'kd_dokter');
        } else {
            $rad_docs = collect();
        }
        foreach ($rad_results as $rad) {
            $rad->examiner = $rad_docs[$rad->kd_dokter] ?? '-';
        }

        // Fetch pending radiology requests
        $rad_pending = DB::table('permintaan_radiologi')
            ->where('permintaan_radiologi.no_rawat', $no_rawat)
            ->select('permintaan_radiologi.noorder', 'permintaan_radiologi.tgl_permintaan', 'permintaan_radiologi.jam_permintaan', 'permintaan_radiologi.dokter_perujuk')
            ->orderBy('permintaan_radiologi.tgl_permintaan', 'desc')
            ->get();

        $pending_doc_ids = $rad_pending->pluck('dokter_perujuk')->unique()->toArray();
        if (!empty($pending_doc_ids)) {
            $pending_docs = DB::connection('dokter')->table('dokter')
                ->whereIn('kd_dokter', $pending_doc_ids)
                ->pluck('nm_dokter', 'kd_dokter');
        } else {
            $pending_docs = collect();
        }
        foreach ($rad_pending as $pending) {
            $pending->dokter_perujuk_nama = $pending_docs[$pending->dokter_perujuk] ?? '-';
            
            $pending->test_names = DB::table('permintaan_pemeriksaan_radiologi')
                ->join('jns_perawatan_radiologi', 'permintaan_pemeriksaan_radiologi.kd_jenis_prw', '=', 'jns_perawatan_radiologi.kd_jenis_prw')
                ->where('permintaan_pemeriksaan_radiologi.noorder', $pending->noorder)
                ->pluck('jns_perawatan_radiologi.nm_perawatan')
                ->implode(', ');
        }

        return view('radiologi.request', compact('reg', 'rad_results', 'rad_pending'));
    }

    /**
     * Store Radiology request.
     */
    public function storeRequest(Request $request)
    {
        abort(403, 'Aksi ini tidak diizinkan. Pembuatan rujukan hanya dapat dilakukan melalui aplikasi SIMKES Khanza (JAR).');
    }

    /**
     * Process Request (Execution of examination).
     */
    public function pemeriksaan($noorder)
    {
        $request = DB::table('permintaan_radiologi')
            ->join('reg_periksa', 'permintaan_radiologi.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->where('noorder', $noorder)
            ->select('permintaan_radiologi.*', 'pasien.nm_pasien', 'pasien.no_rkm_medis', 'reg_periksa.kd_pj', 'reg_periksa.status_lanjut')
            ->first();

        if (!$request) return redirect()->back()->with('error', 'Permintaan radiologi tidak ditemukan.');

        $testTypes = DB::table('permintaan_pemeriksaan_radiologi')
            ->join('jns_perawatan_radiologi', 'permintaan_pemeriksaan_radiologi.kd_jenis_prw', '=', 'jns_perawatan_radiologi.kd_jenis_prw')
            ->where('noorder', $noorder)
            ->select('jns_perawatan_radiologi.kd_jenis_prw', 'jns_perawatan_radiologi.nm_perawatan')
            ->get();

        $dokterRadiologi = DB::connection('dokter')->table('dokter')->where('status', '1')->limit(50)->get();
        $petugas = DB::table('petugas')->where('status', '1')->limit(50)->get();

        return view('radiologi.pemeriksaan', compact('request', 'testTypes', 'dokterRadiologi', 'petugas'));
    }

    /**
     * Store execution (periksa_radiologi).
     */
    public function storePemeriksaan(Request $request)
    {
        $request->validate([
            'no_rawat' => 'required',
            'noorder' => 'required',
            'kd_jenis_prw' => 'required|array',
            'kd_dokter' => 'required',
            'tgl_periksa' => 'required|date',
            'jam_periksa' => 'required'
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->kd_jenis_prw as $kd_jenis) {
                // Get price from jns_perawatan_radiologi
                $price = DB::table('jns_perawatan_radiologi')->where('kd_jenis_prw', $kd_jenis)->first();

                DB::table('periksa_radiologi')->insert([
                    'no_rawat'               => $request->no_rawat,
                    'nip'                    => $request->petugas ?? 'Admin',
                    'kd_jenis_prw'           => $kd_jenis,
                    'tgl_periksa'            => $request->tgl_periksa,
                    'jam'                    => $request->jam_periksa,
                    'dokter_perujuk'         => $request->dokter_perujuk,
                    'bagian_rs'              => $price->bagian_rs ?? 0,
                    'bhp'                    => $price->bhp ?? 0,
                    'tarif_perujuk'          => $price->tarif_perujuk ?? 0,
                    'tarif_tindakan_dokter'  => $price->tarif_tindakan_dokter ?? 0,
                    'tarif_tindakan_petugas' => $price->tarif_tindakan_petugas ?? 0,
                    'kso'                    => $price->kso ?? 0,
                    'menejemen'              => $price->menejemen ?? 0,
                    'biaya'                  => $price->total_byr ?? 0,
                    'kd_dokter'              => $request->kd_dokter,
                    'status'                 => $request->status_lanjut == 'Ranap' ? 'Ranap' : 'Ralan',
                    'proyeksi'               => '-',
                    'kV'                     => '-',
                    'mAS'                    => '-',
                    'FFD'                    => '-',
                    'BSF'                    => '-',
                    'inak'                   => '-',
                    'jml_penyinaran'         => '-',
                    'dosis'                  => '-'
                ]);
            }

            DB::commit();
            return redirect('/radiologi')->with('success', 'Pemeriksaan Radiologi berhasil dicatat.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mencatat pemeriksaan: ' . $e->getMessage());
        }
    }

    /**
     * Display input form for radiology expertise.
     */
    public function input($no_rawat, $tgl, $jam)
    {
        $no_rawat = urldecode($no_rawat);
        $tgl = urldecode($tgl);
        $jam = urldecode($jam);

        $exam = DB::table('periksa_radiologi')
            ->join('reg_periksa', 'periksa_radiologi.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('jns_perawatan_radiologi', 'periksa_radiologi.kd_jenis_prw', '=', 'jns_perawatan_radiologi.kd_jenis_prw')
            ->where([
                ['periksa_radiologi.no_rawat', $no_rawat],
                ['periksa_radiologi.tgl_periksa', $tgl],
                ['periksa_radiologi.jam', $jam],
            ])
            ->select('periksa_radiologi.*', 'pasien.nm_pasien', 'pasien.no_rkm_medis', 'jns_perawatan_radiologi.nm_perawatan')
            ->first();

        if (!$exam) return redirect()->back()->with('error', 'Data pemeriksaan tidak ditemukan atau belum dicatat sebagai tindakan.');

        // Get existing expertise if any
        $expertise = DB::table('hasil_radiologi')
            ->where([
                ['no_rawat', $no_rawat],
                ['tgl_periksa', $tgl],
                ['jam', $jam],
            ])->first();

        return view('radiologi.input', compact('exam', 'expertise'));
    }

    /**
     * Store radiology expertise.
     */
    public function store(Request $request)
    {
        $request->validate([
            'no_rawat' => 'required',
            'tgl_periksa' => 'required',
            'jam' => 'required',
            'hasil' => 'required'
        ]);

        try {
            DB::table('hasil_radiologi')->updateOrInsert(
                [
                    'no_rawat'    => $request->no_rawat,
                    'tgl_periksa' => $request->tgl_periksa,
                    'jam'         => $request->jam
                ],
                [
                    'hasil'       => $request->hasil
                ]
            );

            return redirect('/radiologi')->with('success', 'Hasil expertise radiologi berhasil disimpan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan hasil: ' . $e->getMessage());
        }
    }
}
