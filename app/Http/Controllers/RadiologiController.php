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
            ->where('no_rawat', $no_rawat)
            ->select('reg_periksa.*', 'pasien.nm_pasien')
            ->first();

        if (!$reg) return redirect()->back()->with('error', 'Data pendaftaran tidak ditemukan.');

        $jenis_pemeriksaan = DB::table('jns_perawatan_radiologi')
            ->where('status', '1')
            ->orderBy('nm_perawatan', 'asc')
            ->get();

        return view('radiologi.request', compact('reg', 'jenis_pemeriksaan'));
    }

    /**
     * Store Radiology request.
     */
    public function storeRequest(Request $request)
    {
        $request->validate([
            'no_rawat' => 'required',
            'kd_jenis_prw' => 'required|array|min:1'
        ]);

        try {
            DB::beginTransaction();

            $prefix = 'PR' . date('Ymd');
            $last = DB::table('permintaan_radiologi')
                ->where('noorder', 'like', $prefix . '%')
                ->orderBy('noorder', 'desc')
                ->first();
            
            if ($last) {
                $last_seq = (int) substr($last->noorder, -4);
                $next_seq = str_pad($last_seq + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $next_seq = '0001';
            }
            $noorder = $prefix . $next_seq;

            $reg = DB::table('reg_periksa')->where('no_rawat', $request->no_rawat)->first();
            $status = $reg->status_lanjut == 'Ranap' ? 'ranap' : 'ralan';

            DB::table('permintaan_radiologi')->insert([
                'noorder'          => $noorder,
                'no_rawat'         => $request->no_rawat,
                'tgl_permintaan'   => date('Y-m-d'),
                'jam_permintaan'   => date('H:i:s'),
                'tgl_sampel'       => date('Y-m-d'),
                'jam_sampel'       => date('H:i:s'),
                'tgl_hasil'        => date('Y-m-d'),
                'jam_hasil'        => date('H:i:s'),
                'dokter_perujuk'   => $reg->kd_dokter,
                'status'           => $status,
                'informasi_tambahan' => '-',
                'diagnosa_klinis'  => '-'
            ]);

            foreach ($request->kd_jenis_prw as $kd_jenis) {
                DB::table('permintaan_pemeriksaan_radiologi')->insert([
                    'noorder'      => $noorder,
                    'kd_jenis_prw' => $kd_jenis,
                    'stts_bayar'   => 'Belum'
                ]);
            }

            DB::commit();
            return redirect('/radiologi')->with('success', 'Rujukan Radiologi berhasil dibuat. No Order: ' . $noorder);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal membuat rujukan: ' . $e->getMessage());
        }
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
