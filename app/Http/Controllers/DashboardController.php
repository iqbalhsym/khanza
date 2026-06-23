<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the hospital dashboard with real-time metrics.
     */
    public function index()
    {
        $today = Carbon::today()->toDateString();
        $yesterday = Carbon::yesterday()->toDateString();

        // 1. Total Pasien Hari Ini
        $stats['total_pasien'] = DB::table('reg_periksa')
            ->where('tgl_registrasi', $today)
            ->count();
            
        $yesterday_total = DB::table('reg_periksa')
            ->where('tgl_registrasi', $yesterday)
            ->count();
        $stats['total_pasien_diff'] = $stats['total_pasien'] - $yesterday_total;

        // 2. Rawat Jalan Today
        $stats['ralan'] = DB::table('reg_periksa')
            ->where('tgl_registrasi', $today)
            ->where('status_lanjut', 'Ralan')
            ->count();
        $yesterday_ralan = DB::table('reg_periksa')
            ->where('tgl_registrasi', $yesterday)
            ->where('status_lanjut', 'Ralan')
            ->count();
        $stats['ralan_diff'] = $stats['ralan'] - $yesterday_ralan;

        // 3. Rawat Inap (Active)
        $stats['ranap'] = DB::table('kamar_inap')
            ->where('stts_pulang', '-')
            ->count();
        // For trend, we look at admissions today
        $stats['ranap_new'] = DB::table('kamar_inap')
            ->where('tgl_masuk', $today)
            ->count();

        // 4. Bed Status (BOR)
        $total_beds = DB::table('kamar')->where('statusdata', '1')->count();
        $occupied_beds = DB::table('kamar')->where('status', 'ISI')->count();
        $stats['available_beds'] = $total_beds - $occupied_beds;
        $stats['bor'] = $total_beds > 0 ? round(($occupied_beds / $total_beds) * 100, 1) : 0;

        // 5. Prescriptions Today
        $stats['farmasi'] = DB::table('resep_obat')
            ->where('tgl_perawatan', $today)
            ->count();
        $yesterday_farmasi = DB::table('resep_obat')
            ->where('tgl_perawatan', $yesterday)
            ->count();
        $stats['farmasi_diff'] = $stats['farmasi'] - $yesterday_farmasi;

        // 6. Recent Activities
        $recent_activities = DB::table(DB::raw('reg_periksa FORCE INDEX (reg_periksa_tgl_jam_index)'))
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->select(
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.stts',
                'pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'poliklinik.nm_poli',
                'dokter.nm_dokter'
            )
            ->orderBy('reg_periksa.tgl_registrasi', 'desc')
            ->orderBy('reg_periksa.jam_reg', 'desc')
            ->limit(8)
            ->get();

        return view('dashboard.index', compact('stats', 'recent_activities'));
    }
}
