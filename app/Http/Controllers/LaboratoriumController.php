<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaboratoriumController extends Controller
{
    /**
     * Display Laboratory Queue (requests from doctors).
     */
    /**
     * Display Laboratorium Dashboard with Tabs (Queue & Results).
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'antrian');
        
        if ($tab === 'hasil') {
            // Data for "Hasil Selesai"
            $query = DB::table('detail_periksa_lab')
                ->join('reg_periksa', 'detail_periksa_lab.no_rawat', '=', 'reg_periksa.no_rawat')
                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->select(
                    'detail_periksa_lab.tgl_periksa as tgl_permintaan',
                    'detail_periksa_lab.jam as jam_permintaan',
                    'detail_periksa_lab.no_rawat',
                    'pasien.nm_pasien',
                    'reg_periksa.kd_dokter',
                    DB::raw("'selesai' as stts_data")
                )
                ->distinct();
        } else {
            // Data for "Antrian Permintaan"
            $query = DB::table('permintaan_lab')
                ->join('reg_periksa', 'permintaan_lab.no_rawat', '=', 'reg_periksa.no_rawat')
                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->select(
                    'permintaan_lab.*',
                    'pasien.nm_pasien',
                    'permintaan_lab.dokter_perujuk as kd_dokter',
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

        // Map doctor names in memory
        $kd_dokters = collect($data->items())->pluck('kd_dokter')->unique()->filter()->toArray();
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

        return view('laboratorium.index', compact('data', 'tab'));
    }

    /**
     * Display completed lab results.
     */
    public function hasil(Request $request)
    {
        $query = DB::table('periksa_lab')
            ->join('reg_periksa', 'periksa_lab.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('jns_perawatan_lab', 'periksa_lab.kd_jenis_prw', '=', 'jns_perawatan_lab.kd_jenis_prw')
            ->select(
                'periksa_lab.tgl_periksa as tgl_permintaan',
                'periksa_lab.jam as jam_permintaan',
                'periksa_lab.no_rawat',
                DB::raw("'' as noorder"),
                'pasien.nm_pasien',
                'jns_perawatan_lab.nm_perawatan',
                'periksa_lab.kd_dokter'
            );

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('pasien.nm_pasien', 'like', "%{$search}%")
                  ->orWhere('periksa_lab.no_rawat', 'like', "%{$search}%");
            });
        }

        $data = $query->orderBy('periksa_lab.tgl_periksa', 'desc')
            ->orderBy('periksa_lab.jam', 'desc')
            ->paginate(20)
            ->withQueryString();

        // Map doctor names in memory
        $kd_dokters = collect($data->items())->pluck('kd_dokter')->unique()->filter()->toArray();
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

        $tab = 'hasil';
        return view('laboratorium.index', compact('data', 'tab'));
    }

    /**
     * Display input form for lab results.
     */
    public function input($noorder)
    {
        $request = DB::table('permintaan_lab')
            ->join('reg_periksa', 'permintaan_lab.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->where('noorder', $noorder)
            ->select('permintaan_lab.*', 'pasien.nm_pasien', 'pasien.no_rkm_medis', 'pasien.tgl_lahir', 'pasien.jk')
            ->first();

        if (!$request) return redirect()->back()->with('error', 'Permintaan lab tidak ditemukan.');

        // Get test types requested
        $testTypes = DB::table('permintaan_pemeriksaan_lab')
            ->join('jns_perawatan_lab', 'permintaan_pemeriksaan_lab.kd_jenis_prw', '=', 'jns_perawatan_lab.kd_jenis_prw')
            ->where('noorder', $noorder)
            ->select('jns_perawatan_lab.kd_jenis_prw', 'jns_perawatan_lab.nm_perawatan')
            ->get();

        foreach ($testTypes as $type) {
            $type->templates = DB::table('template_laboratorium')
                ->where('kd_jenis_prw', $type->kd_jenis_prw)
                ->orderBy('urut', 'asc')
                ->get();
        }

        // Get lab staff (petugas) for nip
        $petugas_list = DB::table('petugas')->select('nip', 'nama')->where('status', '1')->get();

        // Get doctors list
        $dokter_list = DB::connection('dokter')->table('dokter')->select('kd_dokter', 'nm_dokter')->where('status', '1')->get();

        return view('laboratorium.input', compact('request', 'testTypes', 'petugas_list', 'dokter_list'));
    }

    /**
     * Store lab results.
     */
    public function store(Request $request)
    {
        $request->validate([
            'no_rawat' => 'required',
            'noorder' => 'required',
            'nip' => 'required',
            'tgl_periksa' => 'required|date',
            'jam_periksa' => 'required',
            'hasil' => 'required|array'
        ]);

        try {
            DB::beginTransaction();

            // 1. Insert into periksa_lab (header) for each distinct kd_jenis_prw
            if ($request->has('kd_jenis_prw')) {
                foreach ($request->kd_jenis_prw as $kd_jenis) {
                    $exists = DB::table('periksa_lab')
                        ->where([
                            ['no_rawat', $request->no_rawat],
                            ['kd_jenis_prw', $kd_jenis],
                            ['tgl_periksa', $request->tgl_periksa],
                            ['jam', $request->jam_periksa]
                        ])->exists();

                    if (!$exists) {
                        $price = DB::table('jns_perawatan_lab')->where('kd_jenis_prw', $kd_jenis)->first();
                        DB::table('periksa_lab')->insert([
                            'no_rawat'               => $request->no_rawat,
                            'nip'                    => $request->nip,
                            'kd_jenis_prw'           => $kd_jenis,
                            'tgl_periksa'            => $request->tgl_periksa,
                            'jam'                    => $request->jam_periksa,
                            'dokter_perujuk'         => $request->dokter_perujuk ?? '-',
                            'kd_dokter'              => $request->kd_dokter ?? '-',
                            'status'                 => $request->status_pasien ?? 'Ralan',
                            'kategori'               => 'PK',
                            'biaya'                  => $price->total_byr ?? 0,
                            'bagian_rs'              => $price->bagian_rs ?? 0,
                            'bhp'                    => $price->bhp ?? 0,
                            'tarif_perujuk'          => $price->tarif_perujuk ?? 0,
                            'tarif_tindakan_dokter'  => $price->tarif_tindakan_dokter ?? 0,
                            'tarif_tindakan_petugas' => $price->tarif_tindakan_petugas ?? 0,
                            'kso'                    => $price->kso ?? 0,
                            'menejemen'              => $price->menejemen ?? 0,
                        ]);
                    }
                }
            }

            // 2. Insert into detail_periksa_lab
            foreach ($request->hasil as $id_template => $nilai) {
                if ($nilai === null || $nilai === '') continue;
                
                $template = DB::table('template_laboratorium')->where('id_template', $id_template)->first();
                if (!$template) continue;

                DB::table('detail_periksa_lab')->updateOrInsert(
                    [
                        'no_rawat'     => $request->no_rawat,
                        'kd_jenis_prw' => $template->kd_jenis_prw,
                        'tgl_periksa'  => $request->tgl_periksa,
                        'jam'          => $request->jam_periksa,
                        'id_template'  => $id_template
                    ],
                    [
                        'nilai'          => $nilai,
                        'nilai_rujukan'  => $template->nilai_rujukan_ld ?? '-',
                        'keterangan'     => '-',
                        'bagian_rs'      => $template->bagian_rs ?? 0,
                        'bhp'            => $template->bhp ?? 0,
                        'bagian_perujuk' => $template->bagian_perujuk ?? 0,
                        'bagian_dokter'  => $template->bagian_dokter ?? 0,
                        'bagian_laborat' => $template->bagian_laborat ?? 0,
                        'kso'            => $template->kso ?? 0,
                        'menejemen'      => $template->menejemen ?? 0,
                        'biaya_item'     => $template->biaya_item ?? 0,
                    ]
                );
            }

            // 3. Update status in permintaan_lab and permintaan_pemeriksaan_lab
            DB::table('permintaan_lab')->where('noorder', $request->noorder)->update([
                'tgl_hasil' => $request->tgl_periksa,
                'jam_hasil' => $request->jam_periksa
            ]);
            
            DB::table('permintaan_pemeriksaan_lab')->where('noorder', $request->noorder)->update([
                'stts_bayar' => 'Sudah'
            ]);

            DB::commit();
            return redirect('/laboratorium')->with('success', 'Hasil laboratorium berhasil disimpan untuk order ' . $request->noorder);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan hasil: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display referral form for Lab.
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

        $jenis_pemeriksaan = DB::table('jns_perawatan_lab')
            ->where('status', '1')
            ->orderBy('nm_perawatan', 'asc')
            ->get();

        return view('laboratorium.request', compact('reg', 'jenis_pemeriksaan'));
    }

    /**
     * Store Lab request.
     */
    public function storeRequest(Request $request)
    {
        $request->validate([
            'no_rawat' => 'required',
            'kd_jenis_prw' => 'required|array|min:1'
        ]);

        try {
            DB::beginTransaction();

            // Generate noorder: PKYYYYMMDDXXXX
            $prefix = 'PK' . date('Ymd');
            $last = DB::table('permintaan_lab')
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

            // Insert into permintaan_lab
            DB::table('permintaan_lab')->insert([
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

            // Insert into permintaan_pemeriksaan_lab
            foreach ($request->kd_jenis_prw as $kd_jenis) {
                DB::table('permintaan_pemeriksaan_lab')->insert([
                    'noorder'      => $noorder,
                    'kd_jenis_prw' => $kd_jenis,
                    'stts_bayar'   => 'Belum'
                ]);
            }

            DB::commit();
            return redirect('/laboratorium')->with('success', 'Rujukan Laboratorium berhasil dibuat. No Order: ' . $noorder);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal membuat rujukan: ' . $e->getMessage());
        }
    }

    /**
     * Show detailed laboratory results for a specific encounter.
     */
    public function showResult($no_rawat, $tgl, $jam)
    {
        $no_rawat = urldecode($no_rawat);
        $tgl = urldecode($tgl);
        $jam = urldecode($jam);
        
        $patientInfo = DB::table('periksa_lab')
            ->join('reg_periksa', 'periksa_lab.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->where([
                ['periksa_lab.no_rawat', $no_rawat],
                ['periksa_lab.tgl_periksa', $tgl],
                ['periksa_lab.jam', $jam]
            ])
            ->select('periksa_lab.*', 'reg_periksa.no_rkm_medis', 'pasien.nm_pasien', 'pasien.tgl_lahir', 'pasien.jk')
            ->first();

        if (!$patientInfo) return redirect()->back()->with('error', 'Hasil lab tidak ditemukan.');

        // Get doctor name from connection 'dokter'
        if (!empty($patientInfo->kd_dokter)) {
            $doc = DB::connection('dokter')->table('dokter')->where('kd_dokter', $patientInfo->kd_dokter)->first();
            $patientInfo->nm_dokter = $doc->nm_dokter ?? '-';
        } else {
            $patientInfo->nm_dokter = '-';
        }

        $results = DB::table('detail_periksa_lab')
            ->join('template_laboratorium', 'detail_periksa_lab.id_template', '=', 'template_laboratorium.id_template')
            ->join('jns_perawatan_lab', 'detail_periksa_lab.kd_jenis_prw', '=', 'jns_perawatan_lab.kd_jenis_prw')
            ->where([
                ['no_rawat', $no_rawat],
                ['tgl_periksa', $tgl],
                ['jam', $jam]
            ])
            ->select('detail_periksa_lab.*', 'detail_periksa_lab.nilai as hasil', 'template_laboratorium.Pemeriksaan', 'template_laboratorium.satuan', 'template_laboratorium.nilai_rujukan_ld', 'template_laboratorium.nilai_rujukan_la', 'jns_perawatan_lab.nm_perawatan')
            ->orderBy('id_template', 'asc')
            ->get();

        return view('laboratorium.view_hasil', compact('patientInfo', 'results'));
    }
}
