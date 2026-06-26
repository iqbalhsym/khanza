<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RawatJalanController extends Controller
{
    public function index(Request $request)
    {
        $tgl_dari    = $request->query('tgl_dari',    date('Y-m-d'));
        $tgl_sampai  = $request->query('tgl_sampai',  date('Y-m-d'));
        $perPage     = $request->query('per_page', 20);
        
        $kd_dokter = session('user')->kd_dokter ?? null;

        // Pastikan tgl_dari tidak lebih dari tgl_sampai
        if ($tgl_dari > $tgl_sampai) {
            $tgl_sampai = $tgl_dari;
        }

        // Global/Scoped status counts for date range
        $statsQuery = \DB::table('reg_periksa')
            ->whereBetween('tgl_registrasi', [$tgl_dari, $tgl_sampai]);
            
        if ($kd_dokter) {
            $statsQuery->where('kd_dokter', $kd_dokter);
        }
        
        $stats = $statsQuery->selectRaw("
                count(*) as total,
                sum(case when stts = 'Belum' then 1 else 0 end) as belum,
                sum(case when stts = 'Sudah' then 1 else 0 end) as sudah
            ")
            ->first();

        $totalKunjungan = $stats->total ?? 0;
        $totalBelum = $stats->belum ?? 0;
        $totalSudah = $stats->sudah ?? 0;

        $antrianQuery = \DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->whereBetween('reg_periksa.tgl_registrasi', [$tgl_dari, $tgl_sampai]);
            
        if ($kd_dokter) {
            $antrianQuery->where('reg_periksa.kd_dokter', $kd_dokter);
        }

        $antrian = $antrianQuery->select(
                'reg_periksa.no_rawat', 'reg_periksa.no_reg', 'reg_periksa.jam_reg',
                'reg_periksa.stts', 'reg_periksa.tgl_registrasi', 'reg_periksa.kd_dokter',
                'pasien.no_rkm_medis', 'pasien.nm_pasien', 'pasien.umur', 'pasien.jk',
                'pasien.special_precautions', 'pasien.special_precautions_color',
                'poliklinik.nm_poli', 'penjab.png_jawab as jaminan'
            )
            ->orderBy('reg_periksa.tgl_registrasi', 'desc')
            ->orderBy('reg_periksa.jam_reg', 'desc')
            ->paginate($perPage);

        // Ambil nama dokter dari database dokter secara terpisah
        $kd_dokters = collect($antrian->items())->pluck('kd_dokter')->unique()->toArray();
        if (!empty($kd_dokters)) {
            $dokters = DB::connection('dokter')->table('dokter')
                ->whereIn('kd_dokter', $kd_dokters)
                ->pluck('nm_dokter', 'kd_dokter');
        } else {
            $dokters = collect();
        }

        foreach ($antrian->items() as $item) {
            $item->nm_dokter = $dokters[$item->kd_dokter] ?? '-';
        }

        // Append query parameters to pagination links
        $antrian->appends([
            'tgl_dari' => $tgl_dari,
            'tgl_sampai' => $tgl_sampai,
            'per_page' => $perPage
        ]);

        // Tetap kirim $tanggal untuk backward-compat (dipakai breadcrumb dll)
        $tanggal = $tgl_dari;

        return view('rawat_jalan.index', compact(
            'antrian', 'tanggal', 'tgl_dari', 'tgl_sampai', 'perPage',
            'totalKunjungan', 'totalBelum', 'totalSudah'
        ));
    }

    public function create()
    {
        // Untuk MVP, kita sediakan opsi cari 100 pasien terakhir agar form tidak terlalu berat
        $pasien = \DB::table('pasien')
            ->select('no_rkm_medis', 'nm_pasien', 'no_ktp', 'alamat')
            ->orderByRaw('CAST(no_rkm_medis AS UNSIGNED) DESC')
            ->limit(100)
            ->get();

        $poliklinik = \DB::table('poliklinik')->where('status', '1')->get();
        $dokter = DB::connection('dokter')->table('dokter')->where('status', '1')->get();
        $penjab = \DB::table('penjab')->where('status', '1')->get();

        return view('rawat_jalan.create', compact('pasien', 'poliklinik', 'dokter', 'penjab'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_rkm_medis' => 'required|exists:pasien,no_rkm_medis',
            'kd_poli'      => 'required|exists:poliklinik,kd_poli',
            'kd_dokter'    => 'required|exists:dokter,kd_dokter',
            'kd_pj'        => 'required|exists:penjab,kd_pj'
        ]);

        try {
            $tgl_registrasi = date('Y-m-d');
            $jam_reg = date('H:i:s');
            
            // Format no_rawat: YYYY/MM/DD/00000X
            $prefix = date('Y/m/d/');
            $last_rawat = \DB::table('reg_periksa')
                ->where('no_rawat', 'like', $prefix . '%')
                ->orderBy('no_rawat', 'desc')
                ->first();
                
            if ($last_rawat) {
                // Ekstrak 6 digit terakhir
                $last_seq = (int) substr($last_rawat->no_rawat, -6);
                $next_seq = str_pad($last_seq + 1, 6, '0', STR_PAD_LEFT);
            } else {
                $next_seq = '000001';
            }
            $no_rawat = $prefix . $next_seq;

            // Hitung antrian per dokter dan poli hari ini (no_reg) --> Maksimal 3 digit misal 001
            $last_reg = \DB::table('reg_periksa')
                ->where('tgl_registrasi', $tgl_registrasi)
                ->where('kd_poli', $request->kd_poli)
                ->where('kd_dokter', $request->kd_dokter)
                ->count();
            $no_reg = str_pad($last_reg + 1, 3, '0', STR_PAD_LEFT);
            
            // Ambil data pasien
            $pasien = \DB::table('pasien')->where('no_rkm_medis', $request->no_rkm_medis)->first();

            // Status daftar: Baru atau Lama
            $cek_lama = \DB::table('reg_periksa')->where('no_rkm_medis', $request->no_rkm_medis)->exists();
            $stts_daftar = $cek_lama ? 'Lama' : 'Baru';

            // Ekstrak numerik unit umur (misal dari "25 Th 0 Bl 0 Hr")
            $umurstr = str_replace('Th', '', explode(' ', $pasien->umur)[0]);
            $umurdaftar = (int) $umurstr;

            \DB::table('reg_periksa')->insert([
                'no_reg'         => $no_reg,
                'no_rawat'       => $no_rawat,
                'tgl_registrasi' => $tgl_registrasi,
                'jam_reg'        => $jam_reg,
                'kd_dokter'      => $request->kd_dokter,
                'no_rkm_medis'   => $request->no_rkm_medis,
                'kd_poli'        => $request->kd_poli,
                'p_jawab'        => $pasien->namakeluarga ?: 'Sendiri',
                'almt_pj'        => $pasien->alamatpj ?: $pasien->alamat,
                'hubunganpj'     => $pasien->keluarga ?: 'Sendiri',
                'biaya_reg'      => 0, // Bypass
                'stts'           => 'Belum',
                'stts_daftar'    => $stts_daftar,
                'status_lanjut'  => 'Ralan',
                'kd_pj'          => $request->kd_pj,
                'umurdaftar'     => $umurdaftar,
                'sttsumur'       => 'Th',
                'status_bayar'   => 'Belum Bayar',
                'status_poli'    => $stts_daftar
            ]);

            return redirect('/rawat-jalan')->with('success', 'Pendaftaran Berhasil. No Antrian: ' . $no_reg);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mendaftar: ' . $e->getMessage());
        }
    }

    public function showRegisteredDetail($no_rawat)
    {
        $no_rawat = urldecode($no_rawat);

        // Fetch Main Registration Data (Tanpa join dokter)
        $data = \DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->leftJoin('pemeriksaan_ralan', 'reg_periksa.no_rawat', '=', 'pemeriksaan_ralan.no_rawat')
            ->where('reg_periksa.no_rawat', $no_rawat)
            ->select(
                'reg_periksa.no_rawat', 'reg_periksa.tgl_registrasi', 'reg_periksa.jam_reg', 'reg_periksa.p_jawab', 'reg_periksa.biaya_reg', 'reg_periksa.kd_dokter',
                'pasien.no_rkm_medis', 'pasien.nm_pasien', 'pasien.tgl_lahir', 'pasien.umur', 'pasien.jk', 'pasien.agama', 'pasien.alamat', 'pasien.no_tlp',
                'pasien.special_precautions', 'pasien.special_precautions_color',
                'poliklinik.nm_poli', 'penjab.png_jawab as jaminan',
                'pemeriksaan_ralan.keluhan'
            )
            ->first();

        if (!$data) {
            return redirect('/rawat-jalan')->with('error', 'Data pasien tidak ditemukan.');
        }

        // Ambil nama DPJP Utama dari database dokter
        $main_doc = DB::connection('dokter')->table('dokter')->where('kd_dokter', $data->kd_dokter)->first();
        $data->dpjp_1 = $main_doc->nm_dokter ?? '-';

        // Fetch Total Invoice (Biaya Registrasi + Tindakan Ralan dll) - Simplified for MVP
        $total_tindakan_dr = \DB::table('rawat_jl_dr')->where('no_rawat', $no_rawat)->sum('biaya_rawat');
        $total_tindakan_pr = \DB::table('rawat_jl_pr')->where('no_rawat', $no_rawat)->sum('biaya_rawat');
        $data->total_invoice = $data->biaya_reg + $total_tindakan_dr + $total_tindakan_pr;

        // Fetch Additional Doctors (DPJP 2-7) - Tanpa join dokter
        $dpjp_tambahan = \DB::table('reg_dpjp_tambahan')
            ->where('reg_dpjp_tambahan.no_rawat', $no_rawat)
            ->select('reg_dpjp_tambahan.urutan', 'reg_dpjp_tambahan.kd_dokter')
            ->orderBy('reg_dpjp_tambahan.urutan', 'asc')
            ->get();

        // Map nama dokter tambahan
        $add_doc_ids = $dpjp_tambahan->pluck('kd_dokter')->unique()->toArray();
        if (!empty($add_doc_ids)) {
            $add_docs = DB::connection('dokter')->table('dokter')
                ->whereIn('kd_dokter', $add_doc_ids)
                ->pluck('nm_dokter', 'kd_dokter');
        } else {
            $add_docs = collect();
        }
        foreach ($dpjp_tambahan as $item) {
            $item->nm_dokter = $add_docs[$item->kd_dokter] ?? '-';
        }
        $dpjp_tambahan = $dpjp_tambahan->keyBy('urutan');

        // All active doctors for dropdowns
        $dokters = DB::connection('dokter')->table('dokter')->where('status', '1')->get();

        // Fetch Patient Identifications
        $identifications = \DB::table('reg_patient_identification')
            ->where('no_rawat', $no_rawat)
            ->orderBy('transaction_date', 'desc')
            ->get();

        // Fetch Allergies
        $allergies = \DB::table('reg_allergies')
            ->where('no_rkm_medis', $data->no_rkm_medis)
            ->orderBy('id', 'desc')
            ->get();

        // 1. Fetch SOAP History (pemeriksaan_ralan) for this Patient (No. RM) - Tanpa join dokter
        $soap_dari = request('soap_dari');
        $soap_sampai = request('soap_sampai');

        $soapQuery = \DB::table('pemeriksaan_ralan')
            ->join('reg_periksa', 'pemeriksaan_ralan.no_rawat', '=', 'reg_periksa.no_rawat')
            ->where('reg_periksa.no_rkm_medis', $data->no_rkm_medis);

        if ($soap_dari) {
            $soapQuery->where('reg_periksa.tgl_registrasi', '>=', $soap_dari);
        }
        if ($soap_sampai) {
            $soapQuery->where('reg_periksa.tgl_registrasi', '<=', $soap_sampai);
        }

        $soap_history = $soapQuery->select(
                'pemeriksaan_ralan.*',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.no_rawat as billing_no',
                'reg_periksa.kd_dokter'
            )
            ->orderBy('reg_periksa.tgl_registrasi', 'desc')
            ->orderBy('reg_periksa.jam_reg', 'desc')
            ->get();

        $soap_doc_ids = $soap_history->pluck('kd_dokter')->unique()->toArray();
        if (!empty($soap_doc_ids)) {
            $soap_docs = DB::connection('dokter')->table('dokter')
                ->whereIn('kd_dokter', $soap_doc_ids)
                ->pluck('nm_dokter', 'kd_dokter');
        } else {
            $soap_docs = collect();
        }
        foreach ($soap_history as $soap) {
            $soap->examiner = $soap_docs[$soap->kd_dokter] ?? '-';
        }

        // 2. Fetch Laboratory Results (completed - from periksa_lab) - Tanpa join dokter
        $lab_results = \DB::table('periksa_lab')
            ->join('jns_perawatan_lab', 'periksa_lab.kd_jenis_prw', '=', 'jns_perawatan_lab.kd_jenis_prw')
            ->where('periksa_lab.no_rawat', $no_rawat)
            ->select(
                'periksa_lab.*',
                'jns_perawatan_lab.nm_perawatan as item_name',
                'periksa_lab.kd_dokter'
            )
            ->orderBy('periksa_lab.tgl_periksa', 'desc')
            ->get();

        $lab_doc_ids = $lab_results->pluck('kd_dokter')->unique()->toArray();
        if (!empty($lab_doc_ids)) {
            $lab_docs = DB::connection('dokter')->table('dokter')
                ->whereIn('kd_dokter', $lab_doc_ids)
                ->pluck('nm_dokter', 'kd_dokter');
        } else {
            $lab_docs = collect();
        }
        foreach ($lab_results as $lab) {
            $lab->examiner = $lab_docs[$lab->kd_dokter] ?? '-';
        }

        // 2b. Fetch Pending Lab Orders (from permintaan_lab) - Tanpa join dokter
        $lab_pending = \DB::table('permintaan_lab')
            ->where('permintaan_lab.no_rawat', $no_rawat)
            ->select(
                'permintaan_lab.noorder',
                'permintaan_lab.tgl_permintaan',
                'permintaan_lab.jam_permintaan',
                'permintaan_lab.dokter_perujuk'
            )
            ->orderBy('permintaan_lab.tgl_permintaan', 'desc')
            ->get();

        $pending_doc_ids = $lab_pending->pluck('dokter_perujuk')->unique()->toArray();
        if (!empty($pending_doc_ids)) {
            $pending_docs = DB::connection('dokter')->table('dokter')
                ->whereIn('kd_dokter', $pending_doc_ids)
                ->pluck('nm_dokter', 'kd_dokter');
        } else {
            $pending_docs = collect();
        }
        foreach ($lab_pending as $pending) {
            $pending->dokter_perujuk_nama = $pending_docs[$pending->dokter_perujuk] ?? '-';
            
            $pending->test_names = \DB::table('permintaan_pemeriksaan_lab')
                ->join('jns_perawatan_lab', 'permintaan_pemeriksaan_lab.kd_jenis_prw', '=', 'jns_perawatan_lab.kd_jenis_prw')
                ->where('permintaan_pemeriksaan_lab.noorder', $pending->noorder)
                ->pluck('jns_perawatan_lab.nm_perawatan')
                ->implode(', ');
        }

        // 3. Fetch Radiology Results (completed) - Tanpa join dokter
        $rad_results = \DB::table('periksa_radiologi')
            ->join('jns_perawatan_radiologi', 'periksa_radiologi.kd_jenis_prw', '=', 'jns_perawatan_radiologi.kd_jenis_prw')
            ->where('periksa_radiologi.no_rawat', $no_rawat)
            ->select(
                'periksa_radiologi.*',
                'jns_perawatan_radiologi.nm_perawatan as item_name',
                'periksa_radiologi.kd_dokter'
            )
            ->orderBy('periksa_radiologi.tgl_periksa', 'desc')
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

        return view('rawat_jalan.detail_registered', compact(
            'data', 'dpjp_tambahan', 'dokters', 'identifications', 'allergies',
            'soap_history', 'lab_results', 'lab_pending', 'rad_results'
        ));
    }

    public function storeDpjpTambahan(Request $request)
    {
        $request->validate([
            'no_rawat' => 'required',
            'dpjp' => 'required|array'
        ]);

        try {
            \DB::beginTransaction();

            $no_rawat = $request->no_rawat;
            // Clear existing for update
            \DB::table('reg_dpjp_tambahan')->where('no_rawat', $no_rawat)->delete();

            // Insert new configured doctors
            foreach ($request->dpjp as $urutan => $kd_dokter) {
                if (!empty($kd_dokter)) {
                    \DB::table('reg_dpjp_tambahan')->insert([
                        'no_rawat' => $no_rawat,
                        'kd_dokter' => $kd_dokter,
                        'urutan' => $urutan
                    ]);
                }
            }

            \DB::commit();
            return redirect()->back()->with('success', 'Data Doctor in Charge berhasil diperbarui.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui DPJP: ' . $e->getMessage());
        }
    }

    public function storeIdentification(Request $request)
    {
        $request->validate([
            'no_rawat'         => 'required',
            'transaction_date' => 'required',
            'examiner_name'    => 'nullable',
            'type'             => 'required',
            'result'           => 'nullable',
            'notes'            => 'nullable'
        ]);

        try {
            \DB::table('reg_patient_identification')->insert([
                'no_rawat'         => $request->no_rawat,
                'transaction_date' => $request->transaction_date,
                'examiner_name'    => $request->examiner_name ?? '-',
                'type'             => $request->type,
                'result'           => $request->result,
                'notes'            => $request->notes,
                'created_by'       => auth()->user()->name ?? 'Admin Web',
                'created_date'     => date('Y-m-d H:i:s')
            ]);
            return redirect()->back()->with('success', 'Patient Identification berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan identifikasi: ' . $e->getMessage());
        }
    }

    public function deleteIdentification($id)
    {
        \DB::table('reg_patient_identification')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Patient Identification berhasil dihapus.');
    }

    public function storeAllergy(Request $request)
    {
        $request->validate([
            'no_rkm_medis' => 'required',
            'allergies' => 'required',
            'type_reaction' => 'required',
            'severity' => 'required'
        ]);

        \DB::table('reg_allergies')->insert([
            'no_rkm_medis' => $request->no_rkm_medis,
            'allergies' => $request->allergies,
            'type_reaction' => $request->type_reaction,
            'severity' => $request->severity,
            'created_date' => date('Y-m-d H:i:s')
        ]);

        return redirect()->back()->with('success', 'Alergi berhasil ditambahkan.');
    }

    public function searchPasien(Request $request)
    {
        $q = trim($request->query('q', ''));
        
        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $results = \DB::table('pasien')
            ->leftJoin('penjab', 'pasien.kd_pj', '=', 'penjab.kd_pj')
            ->select('pasien.*', 'penjab.png_jawab')
            ->where(function($query) use ($q) {
                $query->where('pasien.no_rkm_medis', 'like', '%' . $q . '%')
                      ->orWhere('pasien.nm_pasien', 'like', '%' . $q . '%')
                      ->orWhere('pasien.no_ktp', 'like', '%' . $q . '%');
            })
            ->orderByRaw('CAST(pasien.no_rkm_medis AS UNSIGNED) DESC')
            ->limit(20)
            ->get();

        return response()->json($results);
    }

    public function deleteAllergy($id)
    {
        \DB::table('reg_allergies')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Alergi berhasil dihapus.');
    }
}
